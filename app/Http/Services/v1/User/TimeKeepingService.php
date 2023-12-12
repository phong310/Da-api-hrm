<?php

namespace App\Http\Services\v1\User;

use App\Models\Setting\TimekeepingSetting;
use App\Models\TimeSheet\TimeSheetsLog;
use App\Repositories\Interfaces\Forms\LeaveFormInterface;
use App\Repositories\Interfaces\Forms\RequestChangeTimesheetInterface;
use App\Repositories\Interfaces\HolidayInterface;
use App\Repositories\Interfaces\TimeSheetInterface;
use App\Repositories\Interfaces\TimeSheetLogInterface;
use App\Repositories\Interfaces\WorkingDayInterface;
use App\Transformers\TimeSheetTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class TimeKeepingService extends UserBaseService
{
    /**
     * @var TimeSheetService
     */
    private $timesheetService;

    /**
     * @var TimeSheetInterface
     */
    private $timeSheet;

    /**
     * @var WorkingDayInterface
     */
    private $workingDay;
    /**
     * @var HolidayInterface
     */
    private $holiday;
    /**
     * @var LeaveFormInterface
     */
    private $leaveForm;

    private $requestChangeTimeForm;

    private $timeSheetLog;

    /**
     * @param TimeSheetService $timesheetService
     * @param WorkingDayInterface $workingDay
     * @param HolidayInterface $holiday
     * @param LeaveFormInterface $leaveForm
     * @param TimeSheetInterface $timeSheet
     */
    public function __construct(
        // TimeSheetService $timesheetService,
        WorkingDayInterface $workingDay,
        HolidayInterface $holiday,
        LeaveFormInterface $leaveForm,
        RequestChangeTimesheetInterface $requestChangeTimeForm,
        TimeSheetInterface $timeSheet,
        TimeSheetLogInterface $timeSheetLog
    ) {
        $this->workingDay = $workingDay;
        $this->holiday = $holiday;
        $this->leaveForm = $leaveForm;
        $this->requestChangeTimeForm = $requestChangeTimeForm;
        // $this->timesheetService = $timesheetService;
        $this->timeSheet = $timeSheet;
        $this->timeSheetLog = $timeSheetLog;
    }

    /**
     * @return mixed|void
     */
    protected function setModel()
    {
        $this->model = new TimeSheetsLog();
    }

    /**
     * @param $data
     * @param $filtered
     * @return mixed
     */
    public function setTransformers($data, $filtered)
    {
        $timesheets = collect($filtered)->transformWith(new TimeSheetTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));

        return $timesheets;
    }

    public function setTransformersNotPaginate($data, $minusTotalTimeWorking = false)
    {
        foreach ($data as $val) {
            $totalAllTimeWorking = $this->calculateTotalAllTimeWork($val['date']);
            $val['total_all_time_work'] = $totalAllTimeWorking;
        };

        return collect($data)->transformWith(new TimeSheetTransformer($minusTotalTimeWorking));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    // TODO: Screen Timesheet
    public function index(Request $request)
    {
        $employeeId = $request->user()->employee_id;

        $result = $this->timeSheet->getTimesheetInDate($request->start_date, $request->end_date, $employeeId);

        return $result;
    }

    public function getTotalTimeInMonth(Request $request): \Illuminate\Http\JsonResponse
    {
        $employeeId = $request->user()->employee_id;
        $result = $this->timeSheet->getTimesheetInMonthOfEmployee($request->month, $employeeId);
        $data = $this->timesheetService->getTotalWorkingTime($result);

        return response()->json([
            'total_time_work' => $data['total_time_work'],
            'total_paid_leave' => $data['total_paid_leave'],
            'total_unpaid_leave' => $data['total_unpaid_leave'],
            'total_time_ot' => $data['total_time_ot'],
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $timesheet = $this->query->where('id', $id)->first();
        if (!$timesheet) {
            return response()->json([
                'message' => __('message.not_found'),
            ], 404);
        }
        try {
            $data = $request->only($this->model->getFillable());
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
            Log::error($e->getMessage());

            return $this->errorResponse();
        }
    }

    /**
     * @param Request $request
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $message = '')
    {
        $employee_id = $request->user()->employee_id;
        if (!$employee_id) {
            return response()->json([
                'message' => __('message.not_found'),
            ], 404);
        }

        $date_time = Carbon::now()->timezone('UTC');
        $type = $request->type ?? TimeSheetsLog::TYPE['CHECK_IN'];

        $data = [
            'employee_id' => $employee_id,
            'date_time' => $date_time,
            'type' => $type,
            'note' => $request->note,
            'company_id' => auth()->user()->company_id,
        ];

        $timesheet_log = TimeSheetsLog::create($data);

        return response()->json([
            'message' => __('message.timekeeping_success'),
            'data' => [
                'timesheet_log' => $timesheet_log,
            ],
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function todayTimeSheetLog(Request $request)
    {
        $employee_id = $request->user()->employee_id;
        $date = Carbon::now();
        $date = $date->toDateString();

        return response()->json($this->timesheetLogOnDate($employee_id, $date));
    }

    /**
     * @param $employee_id
     * @param $date
     * @return null[]
     */
    public function timesheetLogOnDate($employee_id, $date)
    {
        return $this->timeSheetLog->getTimeSheetLogOnDate($employee_id, $date);
    }

    /**
     * @param Request $request
     * @return array|bool[]|\Illuminate\Http\JsonResponse
     */
    public function checkHasTimekeeping(Request $request)
    {
        $user = $request->user();
        $employee_id = $user->employee_id;
        $companyId = $user->company_id;
        $date = Carbon::yesterday();

        while (
            !$this->workingDay->showWorkingDayByDate($companyId, $date) ||
            $this->holiday->checkHolidayByDate($companyId, $date)
        ) {
            $date = $date->subDay();
        }

        $dateString = $date->toDateString();
        $res = [
            'date' => $dateString,
        ];

        $leaveForm = $this->leaveForm->showByDate($date, $employee_id);
        $requestChangeTimeFrom = $this->requestChangeTimeForm->showByDate($date, $employee_id);
        $timesheetsLog = $this->timesheetLogOnDate($employee_id, $dateString);

        if ($requestChangeTimeFrom) {
            return array_merge($res, $timesheetsLog, ['forget_timekeeping' => false]);
        }

        if ($leaveForm) {
            if (!$timesheetsLog['last']) {
                return array_merge($res, $timesheetsLog, ['forget_timekeeping' => true]);
            }

            return array_merge($res, $timesheetsLog, ['forget_timekeeping' => false]);
        } else {
            return response()->json(array_merge($res, $timesheetsLog, [
                'forget_timekeeping' => !($timesheetsLog['first'] && $timesheetsLog['last']),
            ]));
        }
    }

    // public function haversineDistance($lat1, $lon1, $lat2, $lon2)
    // {
    //     // lat ~ 20, lon ~ 100

    //     // convert deg to radian
    //     $lat1 = deg2rad($lat1);
    //     $lon1 = deg2rad($lon1);
    //     $lat2 = deg2rad($lat2);
    //     $lon2 = deg2rad($lon2);

    //     // Radius of the Earth (unit meters)
    //     $earthRadius = 6371000; // Áp dụng cho biểu đồ WGS84

    //     // Calculate the differences between coordinates
    //     $latDiff = $lat2 - $lat1;
    //     $lonDiff = $lon2 - $lon1;

    //     //Use the haversine formula to calculate distance
    //     $a = sin($latDiff / 2) * sin($latDiff / 2) + cos($lat1) * cos($lat2) * sin($lonDiff / 2) * sin($lonDiff / 2);
    //     $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    //     $distance = $earthRadius * $c;

    //     return $distance;
    // }

    // public function uploadImage($employee_id, $image)
    // {
    //     $image_url = "";
    //     $folder_name = 'timekeeping/' . $employee_id;

    //     if (request()->hasFile('image')) {
    //         Storage::disk('public')->delete($image);
    //         $image_url = Storage::disk('public')->put(
    //             $folder_name,
    //             $image
    //         );
    //     }

    //     return $image_url;
    // }

    public function calculateTotalAllTimeWork($date)
    {
        $employeeId = Auth::user()->employee_id;
        $timeSheets = $this->timeSheet->showTotalWorkByDate($date, $employeeId);
        $totalTimeSheets = $timeSheets->total_time_work ?? 0;
        $otCoefficients = $this->timeSheet->showOvertimeSalaryCoefficients($date, $employeeId);
        $coefficients = $otCoefficients->overtime->overtimeSalaryCoefficients ?? 0;
        $totalAllWorkingTime = $totalTimeSheets;
        $totalOTCoefficients = 0;

        if ($coefficients) {
            foreach ($coefficients as $val) {
                $cal = $val['total_time_work'];
                $totalOTCoefficients += $cal;
            }

            $totalAllWorkingTime += $totalOTCoefficients;
        }
        return $totalAllWorkingTime;
    }
}
