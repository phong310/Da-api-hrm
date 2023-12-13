<?php

namespace App\Http\Services\v1\Admin;

use App\Http\Services\v1\BaseService;
use App\Models\Form\CompensatoryLeaveHasTimeSheet;
use App\Models\Form\LeaveFormHasTimeSheet;
use App\Models\Setting\SettingLeaveDay;
use App\Models\TimeSheet\TimeSheet;
use App\Models\TimeSheet\TimeSheetsLog;
use App\Repositories\Interfaces\SettingLeaveDayInterface;
use App\Repositories\Interfaces\LaborContract\LaborContractTypeInterface;
use App\Traits\CalculateTime;
use App\Traits\SystemSetting;
use App\Transformers\ManagerTimesheetTransformer;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class TimeSheetService extends BaseService
{
    use SystemSetting;
    use CalculateTime;

    protected $settingLeaveDay;
    protected $laborContractType;

    /**
     * Instantiate a new controller instance.
     *
     * @param SettingLeaveDayInterface $settingLeaveDay
     */
    public function __construct()
    {
        // $this->settingLeaveDay = $settingLeaveDay;
        // $this->laborContractType = $laborContractType;
        parent::__construct();
    }

    /**
     * @return mixed|void
     */
    protected function setModel()
    {
        $this->model = new TimeSheet();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function setTransformers($data)
    {
        $collection = $data->getCollection()->groupBy('employee_id');

        $timesheets = collect($collection)->transformWith(new ManagerTimesheetTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));

        return $timesheets;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function appendFilter()
    {
        $fullname = $this->request->fullname;
        if ($fullname) {
            $this->query->whereHas('personalInformation', function ($query) use ($fullname) {
                return $query->where(DB::raw('concat(first_name," ",last_name)'), 'like', '%' . $fullname . '%');
            });
        }
        $month = $this->request->month;
        if (isset($month)) {
            $yearMonth = explode('-', $month);
            $month = $yearMonth[1];
            $year = $yearMonth[0];
            $this->query->whereYear('date', '=', $year)->whereMonth('date', '=', $month);
        }

        // return parent::index($request);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {

        $data = $request->only($this->model->getFillable());
        $timesheet = $this->query->where('id', $id)->first();

        try {
            if (!$timesheet) {
                return response()->json([
                    'message' => __('message.not_found'),
                ], 404);
            }
            $start = new Carbon($data['start_time']);
            $end = new Carbon($data['end_time']);
            $hourRange = (strtotime($end) - strtotime($start)) / 3600;
            $type = 0;
            if ($hourRange >= 8) $type = 1;
            elseif ($hourRange >= 4) $type = 0.5;

            $timesheet->fill([
                'start_time' => $start->toDateTimeString(),
                'end_time' => $end->toDateTimeString(),
                'type' => $type,
            ]);
            $timesheet->save();

            return response()->json([
                'message' => __('message.update_success'),
                'data' => $timesheet,
            ]);
        } catch (\Exception $e) {
            Log::error($e);

            return $this->errorResponse();
        }
    }

    /**
     * @param $employee
     * @param $date
     */
    public function handleWeekendDay($employee, $date)
    {
        $minTimeCheckIn = $this->minTimeCheckInByEmployee($employee->id, $date);
        $maxTimeCheckOut = $this->maxTimeCheckOutByEmployee($employee->id, $date, $minTimeCheckIn);

        $data = [
            'employee_id' => $employee->id,
            'company_id' => $employee->company_id,
            'date' => $date,
            'start_time' => $minTimeCheckIn,
            'late_time' => null,
            'time_early' => null,
            'total_time_work' => null,
            'end_time' => $maxTimeCheckOut,
            'real_start_time' => $minTimeCheckIn,
            'real_end_time' => $maxTimeCheckOut,
            'real_total_time_work' => null,
            'real_time_early' => null,
            'real_late_time' => null,
        ];
        $timesheet = TimeSheet::query()->where([
            'employee_id' => $data['employee_id'],
            'date' => $data['date'],
        ])->first();

        if ($timesheet || $minTimeCheckIn) {
            $this->updateOrCreateData($data, null, null, 'weekend');
        }
    }

    /**
     * @param $employee
     * @param $date
     * @param $workingDay
     * @param $setting
     */
    public function handleNormalDay($employee, $date, $workingDay, $setting)
    {
        $minTimeCheckIn = $this->minTimeCheckInByEmployee($employee->id, $date);
        $maxTimeCheckOut = $this->maxTimeCheckOutByEmployee($employee->id, $date, $minTimeCheckIn);

        $times = $this->getTotalTimeWork($maxTimeCheckOut, $minTimeCheckIn, $workingDay, $setting, $date);
        $data = [
            'employee_id' => $employee->id,
            'company_id' => $employee->company_id,
            'date' => $date,
            'start_time' => $minTimeCheckIn,
            'end_time' => $maxTimeCheckOut,
            'real_start_time' => $minTimeCheckIn,
            'real_end_time' => $maxTimeCheckOut,
            'real_total_time_work' => $times['total_time_work'],
            'real_time_early' => $times['time_early'],
            'real_late_time' => $times['late_time'],
        ];

        $this->updateOrCreateData($data, $workingDay, $setting, 'schedule', true);
    }

    /**
     * @param $employee
     * @param $date
     * @param $workingDay
     * @param $setting
     */
    public function handleHoliday($employee, $date, $workingDay, $setting)
    {
        $companyId = $employee->company_id;
        $positionId = $employee->position_id;

        // Nếu loại hợp đồng người dùng không thuộc đối tượng được hưởng chế độ nghỉ lễ sẽ không được tính là ngày làm việc
        if (!$this->laborContractType->checkApplyHoliday($companyId, $employee->id)) {
            return;
        }

        $dataFilter = [
            'employee_id' => $employee->id,
            'company_id' => $companyId,
            'date' => $date,
        ];
        $maxTimeCheckOut = $date . ' ' . $workingDay->end_time;
        $minTimeCheckIn = $date . ' ' . $workingDay->start_time;
        $startTimeTZ = $this->convertDateTimeToTZ($minTimeCheckIn, $setting->time_zone);
        $endTimeTZ = $this->convertDateTimeToTZ($maxTimeCheckOut, $setting->time_zone);
        $data = [
            'employee_id' => $employee->id,
            'company_id' => $companyId,
            'date' => $date,
            'late_time' => null,
            'time_early' => null,
            'real_late_time' => null,
            'real_time_early' => null,
            'real_total_time_work' => null,
            'start_time' => $startTimeTZ,
            'end_time' => $endTimeTZ,
            'type' => TimeSheet::TIMESHEET_TYPE['HOLIDAY'],
        ];
        $times = $this->getTotalTimeWork($endTimeTZ, $startTimeTZ, $workingDay, $setting, $date);
        $data['total_time_work'] = $times['total_time_work'];

        TimeSheet::query()->updateOrCreate($dataFilter, $data);
    }



    /**
     * @param $employee
     * @param $date
     * @param $compensatoryWorkingDay
     * @param $setting
     */
    public function handleCompensatoryWorkingDay($employee, $date, $compensatoryWorkingDay, $setting)
    {
        $minTimeCheckIn = $this->minTimeCheckInByEmployee($employee->id, $date);
        $maxTimeCheckOut = $this->maxTimeCheckOutByEmployee($employee->id, $date, $minTimeCheckIn);

        $times = $this->getTotalTimeWork($maxTimeCheckOut, $minTimeCheckIn, $compensatoryWorkingDay, $setting, $date);
        $data = [
            'employee_id' => $employee->id,
            'company_id' => $employee->company_id,
            'date' => $date,
            'start_time' => $minTimeCheckIn,
            'end_time' => $maxTimeCheckOut,
            'real_start_time' => $minTimeCheckIn,
            'real_end_time' => $maxTimeCheckOut,
            'real_total_time_work' => $times['total_time_work'],
            'real_time_early' => $times['time_early'],
            'real_late_time' => $times['late_time'],
        ];

        $this->updateOrCreateData($data, $compensatoryWorkingDay, $setting, 'schedule', true);
    }

    /**
     * @param $employeeId
     * @return mixed
     */
    public function minTimeCheckInByEmployee($employeeId, $date)
    {
        $date = Carbon::createFromFormat('Y-m-d', $date);
        $item = TimeSheetsLog::query()
            ->whereDate('date_time', '=', $date)
            ->where([
                'employee_id' => $employeeId,
            ])
            ->orderBy('date_time', 'asc')
            ->first();
        if ($item) {
            return $item->date_time;
        }

        return null;
    }

    /**
     * @param $employeeId
     * @return mixed
     */
    public function maxTimeCheckOutByEmployee($employeeId, $date, $minTimeCheckIn)
    {
        $date = Carbon::createFromFormat('Y-m-d', $date);
        $item = TimeSheetsLog::query()
            ->whereDate('date_time', '=', $date)
            ->where([
                'employee_id' => $employeeId,
            ])
            ->where('date_time', '!=', $minTimeCheckIn)
            ->orderBy('date_time', 'desc')
            ->first();

        if ($item) {
            return $item->date_time;
        }

        return null;
    }

    /**
     * @param $maxTimeCheckOut
     * @param $minTimeCheckIn
     * @param $workingDay
     * @param $setting
     * @param $date
     * @return null[]
     */
    public function getTotalTimeWork($maxTimeCheckOut, $minTimeCheckIn, $workingDay, $setting, $date)
    {
        //        dd($workingDay);
        $times = [
            'time_early' => null,
            'late_time' => null,
            'total_time_work' => null,
        ];

        $timezone = $setting->time_zone;

        $settingStartWork = $this->convertDateTimeToTZ($date . ' ' . $workingDay->start_time, $timezone);
        $settingEndWork = $this->convertDateTimeToTZ($date . ' ' . $workingDay->end_time, $timezone);
        $startLunchBreak = $this->convertDateTimeToTZ($date . ' ' . $workingDay->start_lunch_break, $timezone);
        $endLunchBreak = $this->convertDateTimeToTZ($date . ' ' . $workingDay->end_lunch_break, $timezone);

        $checkInAt = $this->convertDateTimeToTZ($minTimeCheckIn);
        $checkOutAt = $this->convertDateTimeToTZ($maxTimeCheckOut);

        $startTimeCalculate = $settingStartWork;
        $endTimeCalculate = $settingEndWork;

        // Nếu không check in check out
        if (!$minTimeCheckIn && $maxTimeCheckOut) {
            return $times;
        }

        //1. Neu co check in ma quen check out
        if ($minTimeCheckIn && !$maxTimeCheckOut) {
            //Neu check in truoc thoi gian lam viec
            if ($checkInAt <= $settingStartWork) {
                return $times;
            }
            //Neu check in sau thoi gian bat dau lam va truoc an trua
            if ($checkInAt <= $startLunchBreak) {
                $times['late_time'] = $checkInAt->floatDiffInMinutes($settingStartWork);

                return $times;
            }

            //Neu check in sau an trua va sau gio an trua
            if ($checkInAt <= $endLunchBreak) {
                $times['late_time'] = $startLunchBreak->floatDiffInMinutes($settingStartWork);

                return $times;
            }

            //Neu check in sau thoi gian an trua
            $times['late_time'] = $startLunchBreak->floatDiffInMinutes($settingStartWork) + $endLunchBreak->floatDiffInMinutes($checkInAt);

            return $times;
        }

        //2. NEU check in sau gio lam viec cua cong ty se ko tinh
        if ($checkInAt >= $settingEndWork) {
            $times['late_time'] = $startLunchBreak->floatDiffInMinutes($settingStartWork) + $endLunchBreak->floatDiffInMinutes($settingEndWork);

            return $times;
        }

        //3. Neu check out truoc thoi gian bat dau lam cua cong ty
        if ($checkOutAt <= $settingStartWork) {
            $times['time_early'] = $startLunchBreak->floatDiffInMinutes($settingStartWork) + $endLunchBreak->floatDiffInMinutes($settingEndWork);

            return $times;
        }

        //-------------- Neu ton tai check in va check out -----------------------
        //4. Check neu check in sau gio bat dau lam viec
        $isCheckLateTime = true;
        if ($checkInAt > $settingStartWork) {
            //Thoi gian check in muon hon thoi gian bat dau lam viec truoc thoi gian an trua
            if ($startLunchBreak >= $checkInAt) {
                $startTimeCalculate = $checkInAt;
                $isCheckLateTime = false;
                $times['late_time'] = $settingStartWork->floatDiffInMinutes($checkInAt);
            }

            //Neu check in sau thoi gian bat dau lam va truoc an trua
            if ($checkInAt <= $startLunchBreak && $isCheckLateTime) {
                $startTimeCalculate = $checkInAt;
                $isCheckLateTime = false;
                $times['late_time'] = $checkInAt->floatDiffInMinutes($settingStartWork);
            }

            //Neu check truoc an trua va sua gio an trua
            if ($checkInAt <= $endLunchBreak && $isCheckLateTime) {
                $startTimeCalculate = $endLunchBreak;
                $isCheckLateTime = false;
                $times['late_time'] = $startLunchBreak->floatDiffInMinutes($settingStartWork);
            }

            //Neu check in sau thoi an trua
            if ($checkInAt >= $endLunchBreak && $isCheckLateTime) {
                $startTimeCalculate = $checkInAt;
                $times['late_time'] = $startLunchBreak->floatDiffInMinutes($settingStartWork) + $endLunchBreak->floatDiffInMinutes($checkInAt);
            }
        }
        //5. Check neu check out truoc thoi gian ket thuc lam viec trong ngay
        $isCheckTimeEarly = true;
        if ($checkOutAt < $settingEndWork) {
            //Thoi gian check out truoc an trua
            if ($startLunchBreak >= $checkOutAt && $isCheckTimeEarly) {
                $isCheckTimeEarly = false;
                $endTimeCalculate = $checkOutAt;
                $times['time_early'] = $checkOutAt->floatDiffInMinutes($startLunchBreak) + $endLunchBreak->floatDiffInMinutes($settingEndWork);
            }

            //Check out sau o thoi gian an trua
            if ($checkOutAt > $startLunchBreak && $checkOutAt <= $endLunchBreak && $isCheckTimeEarly) {
                $isCheckTimeEarly = false;
                $endTimeCalculate = $endLunchBreak;
                $times['time_early'] = $endLunchBreak->floatDiffInMinutes($settingEndWork);
            }

            //Check out sau thoi gian an trua
            if ($checkOutAt > $endLunchBreak && $isCheckTimeEarly) {
                $endTimeCalculate = $checkOutAt;
                $times['time_early'] = $checkOutAt->floatDiffInMinutes($settingEndWork);
            }
        }
        if ($times['late_time'] > ($settingStartWork->floatDiffInMinutes($settingEndWork) - $startLunchBreak->floatDiffInMinutes($endLunchBreak))) {
            $times['late_time'] = $settingStartWork->floatDiffInMinutes($settingEndWork) - $startLunchBreak->floatDiffInMinutes($endLunchBreak);
        }

        // Neu thoi gian check in va check out deu nam truoc thoi gian bat dau an trua va sau thoi gian an trua
        if (
            $startTimeCalculate <= $startLunchBreak && $endTimeCalculate <= $startLunchBreak ||
            $startTimeCalculate >= $endLunchBreak && $endTimeCalculate >= $endLunchBreak
        ) {
            $times['total_time_work'] = $startTimeCalculate->floatDiffInMinutes($endTimeCalculate);

            return $times;
        }

        $times['total_time_work'] = $startLunchBreak->floatDiffInMinutes($startTimeCalculate) + $endTimeCalculate->floatDiffInMinutes($endLunchBreak);

        return $times;
    }

    /**
     * @param $timesheet
     * @param $data
     * @param $listFormHasTimesheet
     * @param $isSalary
     * @param $timeOff
     * @param $totalTimeUnpaid
     * @param $workingDay
     * @param $setting
     * @return mixed
     */
    public function handleTimeWhenApproval($timesheet, $data, $listFormHasTimesheet, $isSalary, $timeOff, $totalTimeUnpaid, $workingDay, $setting)
    {
        $forms = array_merge($listFormHasTimesheet, [$data]);
        $dateTimes = $this->getDateTimeFromForms($timesheet, $forms, true);
        $dateTimesToCalculate = $this->timeToCalculate($dateTimes);

        $array = [
            'total_time_work' => 0,
            'late_time' => 0,
            'time_early' => 0
        ];

        foreach ($dateTimesToCalculate as $dateTime) {
            $times = $this->getTotalTimeWork($dateTime['end_time'], $dateTime['start_time'], $workingDay, $setting, $data['date']);
            $isEndTimeNull = false;
            // Nếu khoảng đó chứa timesheet và không có checkout
            if ($dateTime['has_timesheet'] && !$timesheet['end_time']) {
                $isEndTimeNull = true;
            }
            $array = $this->handleTimeAfterCalculate($array, $times, $dateTime['after_timesheet'], $dateTime['has_timesheet'], $isEndTimeNull);
        }

        $data = array_merge($data, $array);
        $data['late_time'] = ($data['late_time'] > 0) ? ($data['late_time']) : null;
        $data['time_early'] = ($data['time_early'] > 0) ? ($data['time_early']) : null;
        $data['total_time_work'] = ($data['total_time_work'] - $totalTimeUnpaid > 0) ? ($data['total_time_work'] - $totalTimeUnpaid) : null;

        if ($isSalary) {
            return $data;
        }

        $data['total_time_work'] = ($data['total_time_work'] - $timeOff > 0) ? ($data['total_time_work'] - $timeOff) : null;
        return $data;
    }

    /**
     * @param $data
     * @param $timesheet
     * @param $workingDay
     * @param $setting
     * @param $isCreated
     * @return array|mixed
     */
    public function timesheetWhenApprovalLeaveForm($data, $timesheet, $workingDay, $setting, $isCreated)
    {
        $startAt = $data['start_time'];
        $endAt = $data['end_time'];

        $timeOff = $data['time_off'];
        $isSalary = $data['is_salary'];

        if (isset($data['is_salary'])) {
            unset($data['is_salary']);
        }
        unset($data['time_off']);

        $clHasTimesheet = CompensatoryLeaveHasTimeSheet::query()->where(['timesheet_id' => $timesheet->id])->first();
        $lfHasTimesheets = LeaveFormHasTimeSheet::query()->where(['timesheet_id' => $timesheet->id])->with(['leaveForm'])->get();
        $listFormHasTimesheets = [$clHasTimesheet];
        $totalTimeUnpaid = 0;
        foreach ($lfHasTimesheets as $d) {
            $listFormHasTimesheets[] = $d;
            if (!$d['leaveForm']['is_salary']) {
                $totalTimeUnpaid += $d['time_off'];
            }
        }

        // Nếu phê duyệt ở ngày tương lai hoăc $timesheet chưa được tạo
        if ($isCreated) {
            return $this->handleTimeWhenApproval($timesheet, $data, $listFormHasTimesheets, $isSalary, $timeOff, $totalTimeUnpaid, $workingDay, $setting);
        }

        $times = $this->getTotalTimeWork($endAt, $startAt, $workingDay, $setting, $data['date']);
        $data = array_merge($data, $times);
        if ($isSalary) {
            return $data;
        }
        $data['total_time_work'] = null;

        return $data;
    }

    public function timesheetWhenApprovalCompensatoryLeave($data, $timesheet, $workingDay, $setting, $isCreated)
    {
        $startAt = $data['start_time']; // leave form
        $endAt = $data['end_time'];
        $timeOff = $data['time_off'];
        unset($data['time_off']);

        $lfHasTimesheets = LeaveFormHasTimeSheet::query()->where(['timesheet_id' => $timesheet->id])->with(['leaveForm'])->get();
        $listFormHasTimesheets = [];
        $totalTimeUnpaid = 0;
        foreach ($lfHasTimesheets as $d) {
            $listFormHasTimesheets[] = $d;
            if (!$d['leaveForm']['is_salary']) {
                $totalTimeUnpaid += $d['time_off'];
            }
        }

        // Nếu phê duyệt ở ngày tương lai hoăc $timesheet chưa được tạo
        if ($isCreated) {
            return $this->handleTimeWhenApproval($timesheet, $data, $listFormHasTimesheets, true, $timeOff, $totalTimeUnpaid, $workingDay, $setting);
        }
        $times = $this->getTotalTimeWork($endAt, $startAt, $workingDay, $setting, $data['date']);

        return array_merge($data, $times);
    }

    /**
     * @param $timesheet
     * @param $data
     * @param $workingDay
     * @param $setting
     * @param $dataIsTimesheet
     * @return mixed
     */
    public function handleTimeWhenApprovalRunSchedule($timesheet, $data, $workingDay, $setting, $dataIsTimesheet)
    {
        $lfHasTimesheets = LeaveFormHasTimeSheet::query()->where(['timesheet_id' => $timesheet->id])->with(['leaveForm'])->get();
        $clHasTimesheet = CompensatoryLeaveHasTimeSheet::query()->where(['timesheet_id' => $timesheet->id])->first();

        $listFormHasTimesheets = [$clHasTimesheet];
        $totalTimeUnpaid = 0;
        foreach ($lfHasTimesheets as $d) {
            $listFormHasTimesheets[] = $d;
            if (!$d['leaveForm']['is_salary']) {
                $totalTimeUnpaid += $d['time_off'];
            }
        }

        $dateTimes = $this->getDateTimeFromForms($data, $listFormHasTimesheets, $dataIsTimesheet);

        $dateTimesToCalculate = $this->timeToCalculate($dateTimes);

        $array = [
            'total_time_work' => 0,
            'late_time' => 0,
            'time_early' => 0
        ];

        foreach ($dateTimesToCalculate as $dateTime) {
            $times = $this->getTotalTimeWork($dateTime['end_time'], $dateTime['start_time'], $workingDay, $setting, $data['date']);
            $isEndTimeNull = false;
            if ($dateTime['has_timesheet'] && !$timesheet['end_time']) {
                $isEndTimeNull = true;
            }
            $array = $this->handleTimeAfterCalculate($array, $times, $dateTime['after_timesheet'], $dateTime['has_timesheet'], $isEndTimeNull);
        }

        $data = array_merge($data, $array);
        $data['late_time'] = ($data['late_time'] > 0) ? ($data['late_time']) : null;
        $data['time_early'] = ($data['time_early'] > 0) ? ($data['time_early']) : null;

        $data['total_time_work'] = ($data['total_time_work'] - $totalTimeUnpaid > 0)
            ? ($data['total_time_work'] - $totalTimeUnpaid)
            : null;

        return $data;
    }

    /**
     * @param $data
     * @param $timesheet
     * @param $workingDay
     * @param $setting
     * @param $isCreated
     * @return array
     */
    public function timesheetWhenApprovalRunSchedule($data, $timesheet, $workingDay, $setting, $isCreated, $dataIsTimesheet): array
    {
        $startAt = $data['start_time'];
        $endAt = $data['end_time'];

        if ($isCreated) {
            return $this->handleTimeWhenApprovalRunSchedule($timesheet, $data, $workingDay, $setting, $dataIsTimesheet);
        }

        $times = $this->getTotalTimeWork($endAt, $startAt, $workingDay, $setting, $data['date']);

        return array_merge($data, $times);
    }

    /**
     * @param $data $data = ['date' => $date, 'employee_id' => $employee_id, 'start_time' => $start_time, 'end_time'];
     * @param $workingDay
     * @param $setting
     * @param $action // leave-form, schedule, weekend
     * @param bool $dataIsTimesheet
     * @return TimeSheet|Builder|Model|object|null
     */
    public function updateOrCreateData($data, $workingDay, $setting, $action, bool $dataIsTimesheet = false)
    {
        $timesheet = TimeSheet::query()->where([
            'employee_id' => $data['employee_id'],
            'date' => $data['date'],
        ])->first();
        $isCreated = true;
        if (!$timesheet) {
            $isCreated = false;
            $timesheet = new TimeSheet();
        }

        switch ($action) {
            case 'leave-form':
                $data = $this->timesheetWhenApprovalLeaveForm($data, $timesheet, $workingDay, $setting, $isCreated);
                $data['start_time'] = $timesheet->start_time;
                $data['end_time'] = $timesheet->end_time;
                break;
            case 'compensatory-leave':
                $data = $this->timesheetWhenApprovalCompensatoryLeave($data, $timesheet, $workingDay, $setting, $isCreated);
                $data['start_time'] = $timesheet->start_time;
                $data['end_time'] = $timesheet->end_time;
                break;
            case 'weekend':
                break;
            default: //schedule
                $data = $this->timesheetWhenApprovalRunSchedule($data, $timesheet, $workingDay, $setting, $isCreated, $dataIsTimesheet);
        }
        foreach ($data as $key => $val) {
            $timesheet->$key = $val;
        }
        $timesheet->save();

        return $timesheet;
    }
}
