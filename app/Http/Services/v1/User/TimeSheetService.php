<?php

namespace App\Http\Services\v1\User;

use App\Http\Controllers\Api\v1\User\ManagerController;
use App\Models\Form\LeaveForm;
use App\Models\TimeSheet\TimeSheet;
use App\Repositories\Interfaces\EmployeeInterface;
use App\Repositories\Interfaces\Forms\CompensatoryLeaveInterface;
use App\Repositories\Interfaces\Forms\LeaveFormInterface;
use App\Repositories\Interfaces\Forms\OvertimeInterface;
use App\Repositories\Interfaces\Forms\RequestChangeTimesheetInterface;
use App\Repositories\Interfaces\OvertimeSalaryCoefficientInterface;
use App\Repositories\Interfaces\TimeSheetInterface;
use App\Transformers\TimeSheetTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class TimeSheetService extends UserBaseService
{
    /**
     * @var LeaveFormInterface
     * @var OvertimeInterface
     * @var RequestChangeTimesheetInterface
     */
    private $leaveForm;
    /**
     * @var OvertimeInterface
     */
    private $overtime;
    /**
     * @var RequestChangeTimesheetInterface
     */
    private $requestChangeTimesheet;
    /**
     * @var EmployeeInterface
     */
    private $employee;

    private $timeSheet;
    /**
     * @var CompensatoryLeaveInterface
     */
    private $compensatoryLeave;
    /**
     * @var OvertimeSalaryCoefficientInterface
     */
    private $overtimeSalaryCoefficient;
    private  $requestChangeTimeSheetService;

    /**
     * @param LeaveFormInterface $leaveForm
     * @param OvertimeInterface $overtime
     * @param TimeSheetInterface $timeSheet
     * @param EmployeeInterface $employee
     * @param RequestChangeTimesheetInterface $requestChangeTimesheet
     * @param CompensatoryLeaveInterface $compensatoryLeave
    //  * @param OvertimeSalaryCoefficientInterface $overtimeSalaryCoefficient
     */
    public function __construct(
        LeaveFormInterface $leaveForm,
        OvertimeInterface $overtime,
        TimeSheetInterface $timeSheet,
        EmployeeInterface $employee,
        RequestChangeTimesheetInterface $requestChangeTimesheet,
        CompensatoryLeaveInterface $compensatoryLeave,
        // OvertimeSalaryCoefficientInterface $overtimeSalaryCoefficient,
        RequestChangeTimesheetService $requestChangeTimeSheetService
    ) {
        $this->leaveForm = $leaveForm;
        $this->overtime = $overtime;
        $this->employee = $employee;
        $this->timeSheet = $timeSheet;
        $this->requestChangeTimesheet = $requestChangeTimesheet;
        $this->compensatoryLeave = $compensatoryLeave;
        // $this->overtimeSalaryCoefficient = $overtimeSalaryCoefficient;
        $this->requestChangeTimeSheetService = $requestChangeTimeSheetService;
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
        $collection = $data->getCollection();
        $timesheets = collect($collection)->transformWith(new TimeSheetTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));

        return $timesheets;
    }

    public function setTransformersNotPaginate($data, $minusTotalTimeWorking = false)
    {
        return collect($data)->transformWith(new TimeSheetTransformer($minusTotalTimeWorking));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function appendFilter()
    {
        $employee_id = Auth::user()->employee_id;

        if (isset($request->month)) {
            $yearMonth = explode('-', $request->month);
            $month = $yearMonth[1];
            $year = $yearMonth[0];
            $this->query->whereYear('date', '=', $year)
                ->whereMonth('date', '=', $month)
                ->with([
                    'overtime.overtimeSalaryCoefficients',
                    'leaveFormHasTimesheets.leaveForm.kind_of_leave',
                    'compensatoryLeaveHasTimesheet.compensatoryLeave.kind_of_leave'
                ]);
        }

        $this->query
            ->where('employee_id', $employee_id)
            ->orderBy('start_time', 'DESC');
    }

    /**
     * @param $yearMonth
     * @param $perPage
     * @param $employeeName
     * @return array
     */
    // TODO: Screen Management Timekeeping
    public function employeesByMonth($yearMonth, $perPage, $employeeName)
    {
        $employees = $this->employee->getEmployeesHasTimesheetInMonth($perPage, $employeeName);
        $data = $this->transformData($employees, $yearMonth);
        $paginate = $this->paginateData($employees, $perPage);

        return [
            'data' => $data,
            'meta' => $paginate,
        ];
    }

    /**
     * @param $employees
     * @param $yearMonth
     * @return array
     */
    public function transformData($employees, $yearMonth)
    {
        $dataWithTransform = [];

        foreach ($employees as $employee) {
            $timesheets = $this->timeSheet->getTimesheetInMonthOfEmployee($yearMonth, $employee['id']);
            $data = $this->getTotalWorkingTime($timesheets);

            $transform = $this->setTransformersNotPaginate($timesheets)->toArray();
            $transform = collect($transform['data'])->groupBy('date');
            $transform = $transform->map(function ($item) {
                return $item[0];
            });

            $employeeWithTimeSheets = [
                'employee_id' => $employee['id'] ?? null,
                'job_position' => $employee->position->name ?? null,
                'department' => $employee->department->name ?? null,
                'fullname' => $employee->personalInformation->fullname ?? null,
                'avatar' => $employee->personalInformation->thumbnail_url ?? null,
                'employee_code' => $employee->employee_code ?? null,
                'timesheets' => $transform,
            ];

            $employeeWithTimeSheets['total_time_work'] = $data['total_time_work'];
            $employeeWithTimeSheets['total_time_ot'] = $data['total_time_ot'];
            $employeeWithTimeSheets['total_late_time'] = $data['total_late_time'];
            $employeeWithTimeSheets['total_early_time'] = $data['total_early_time'];

            $dataWithTransform[] = $employeeWithTimeSheets;
        }

        return $dataWithTransform;
    }

    /**
     * @param $items
     * @param int $perPage
     * @return array[]
     */
    public function paginateData($items, int $perPage = 10): array
    {
        $pagination = [
            'count' => count($items),
            'current_page' => $items->currentPage(),
            'per_page' => (int) $perPage,
            'total' => $items->total(),
            'total_pages' => $items->lastPage(),
        ];

        return ['pagination' => $pagination];
    }

    /**
     * @param $form
     * @param $company_id
     * @param $action
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrCreateWhenApprovalOTForm($form, $company_id, $action)
    {
        try {
            DB::beginTransaction();
            $timesheet = TimeSheet::where([
                'date' => $form->date,
                'employee_id' => $form->employee_id,
                'company_id' => $company_id,
            ])->first();

            switch ($action) {
                case ManagerController::ACTION['ACCEPT']:
                    if (!$timesheet) {
                        $timesheet = new TimeSheet();
                    }
                    $timesheet->employee_id = $form->employee_id;
                    $timesheet->date = $form->date;
                    $timesheet->company_id = $company_id;
                    $timesheet->save();

                    $this->overtimeSalaryCoefficient->storeByRangeTime($form);
                    break;
                default: //reject-after-accept
                    if (
                        !$timesheet->start_time &&
                        !$timesheet->end_time &&
                        !$timesheet->total_time_work &&
                        count($timesheet->leaveFormHasTimesheets) == 0 &&
                        !$timesheet->compensatoryLeaveHasTimesheet
                    ) {
                        $timesheet->forceDelete();
                    }

                    $this->overtimeSalaryCoefficient->destroyByOvertimeId($form->id);
            }
            DB::commit();

            return $timesheet;
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    public function checkHasFormByDate($date): array
    {
        $totalOTCoefficients = 0;
        $employeeId = Auth::user()->employee_id;
        $leaveForm = $this->leaveForm->showByDate($date, $employeeId);
        $overTime = $this->overtime->showByDate($date, $employeeId);
        $requestChangeTimesheet = $this->requestChangeTimesheet->showByDate($date, $employeeId);

        if ($requestChangeTimesheet) {
            $requestChangeTimesheet['total_time'] = $this->requestChangeTimeSheetService
                ->totalRequestChangeTime(
                    $requestChangeTimesheet['check_in_time'],
                    $requestChangeTimesheet['check_out_time']
                );
        }

        $compensatoryLeave = $this->compensatoryLeave->showByDate($date, $employeeId);
        $timeSheets = $this->timeSheet->showTotalWorkByDate($date, $employeeId);
        $totalTimeSheets = $timeSheets->total_time_work ?? 0;
        $otCoefficients = $this->timeSheet->showOvertimeSalaryCoefficients($date, $employeeId);
        $coefficients = $otCoefficients->overtime->overtimeSalaryCoefficients ?? 0;
        $totalAllWorkingTime = $totalTimeSheets;

        if ($coefficients) {
            foreach ($coefficients as $val) {
                $cal = $val['total_time_work'];
                $totalOTCoefficients += $cal;
            }

            $totalAllWorkingTime += $totalOTCoefficients;
        }

        return [
            'leave_form' => $leaveForm,
            'overtime' => $overTime,
            'request_change_timesheet' => $requestChangeTimesheet,
            'compensatory_leave' => $compensatoryLeave,
            'total_all_time_work' => $totalAllWorkingTime
        ];
    }

    /**
     * @param $data
     * @return array
     */
    public function getTotalWorkingTime($data): array
    {
        $totalTimeWork = 0;
        $totalPaidLeave = 0;
        $totalUnpaidLeave = 0;
        $totalLateTime = 0;
        $totalEarlyTime = 0;
        $totalTimeOT = [];

        foreach ($data as $d) {
            $totalTimeWork += $d['total_time_work'];
            $totalLateTime += $d['late_time'];
            $totalEarlyTime += $d['time_early'];
            $overtime = $d['overtime'];
            $lfHasTimesheets = $d['leaveFormHasTimesheets'];
            if (count($lfHasTimesheets)) {
                foreach ($lfHasTimesheets as $lfHasTimesheet) {
                    if ($lfHasTimesheet['leaveForm']->is_salary == LeaveForm::PAID_LEAVE['NO']) {
                        $totalUnpaidLeave += $lfHasTimesheet['time_off'];
                    } else {
                        $totalPaidLeave += $lfHasTimesheet['time_off'];
                    }
                }
            }
            $clHasTimesheet = $d['compensatoryLeaveHasTimesheet'];
            if ($clHasTimesheet) {
                $totalPaidLeave += $clHasTimesheet['time_off'];
            }

            $totalTimeOT = $this->getTotalTimeOT($overtime, $totalTimeOT);
        }

        return [
            'total_time_work' => $totalTimeWork,
            'total_paid_leave' => $totalPaidLeave,
            'total_unpaid_leave' => $totalUnpaidLeave,
            'total_late_time' => $totalLateTime,
            'total_early_time' => $totalEarlyTime,
            'total_time_ot' => $totalTimeOT,
        ];
    }

    /**
     * @param $overtime
     * @param $res
     * @return mixed
     */
    public function getTotalTimeOT($overtime, $res)
    {
        $total = $res;

        if ($overtime) {
            foreach ($overtime['overtimeSalaryCoefficients'] as $value) {
                $coefficient_salary = $value['salary_coefficient'];
                if (property_exists((object) $total, $coefficient_salary)) {
                    $total[(string) $coefficient_salary] += $value['total_time_work'];
                } else {
                    $total[(string) $coefficient_salary] = $value['total_time_work'];
                }
            }
        }

        return $total;
    }
}
