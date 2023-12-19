<?php

namespace App\Http\Services\v1\User;

use App\Models\Form\CompensatoryLeave;
use App\Models\Form\LeaveForm;
use App\Models\Form\OverTime;
use App\Models\Form\RequestChangeTimesheet;
use App\Models\LaborContract\LaborContract;
use App\Models\TimeSheet\TimeSheet;
use App\Repositories\Interfaces\Forms\LeaveFormInterface;
use App\Repositories\Interfaces\Forms\OvertimeInterface;
use App\Repositories\Interfaces\Forms\RequestChangeTimesheetInterface;
use App\Repositories\Interfaces\Forms\CompensatoryLeaveInterface;
use App\Repositories\Interfaces\LaborContract\LaborContractInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardService extends UserBaseService
{
    protected $overtime;
    protected $leaveForm;
    protected $requestChangeTimesheet;
    protected $compensatoryLeaveForm;
    protected $laborContractInterface;
    protected $laborContract;

    /**
     * @return mixed|void
     */
    protected function setModel()
    {
        $this->model = new TimeSheet();
    }

    public function __construct(
        OvertimeInterface $overtime,
        LeaveFormInterface $leaveForm,
        RequestChangeTimesheetInterface $requestChangeTimesheet,
        CompensatoryLeaveInterface $compensatoryLeave,
        LaborContractInterface $laborContractInterFace,
    ) {
        $this->overtime = $overtime;
        $this->leaveForm = $leaveForm;
        $this->requestChangeTimesheet = $requestChangeTimesheet;
        $this->compensatoryLeaveForm = $compensatoryLeave;
        $this->laborContractInterface = $laborContractInterFace;
        parent::__construct();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $employee_id = $request->user()->employee_id;
        $today = Carbon::now();
        $year = $today->year;
        $month = $today->month;
        $expire = LaborContract::STATUS['EXPIRTION'];
        $active = LaborContract::STATUS['ACTIVE'];

        if ($request->month) {
            $yearMonth = explode('-', $request->month);
            $month = $yearMonth[1];
            $year = $yearMonth[0];
        }

        $amount['late_time'] = $this->sumTimeSheetByField($employee_id, $year, $month, 'late_time');
        $amount['early_time'] = $this->sumTimeSheetByField($employee_id, $year, $month, 'time_early');
        $amount['working_time'] = $this->sumTimeSheetByField($employee_id, $year, $month, 'total_time_work');
        $amount['over_time'] = $this->sumOverTimeByField($employee_id, $year, $month, 'total_time_work');

        $amount['leave_app'] = $this->totalFormByEmployee($employee_id, $year, $month, LeaveForm::query());
        $amount['compensatory_leave_app'] = $this->totalFormByEmployee($employee_id, $year, $month, CompensatoryLeave::query());
        $amount['over_time_app'] = $this->totalFormByEmployee($employee_id, $year, $month, OverTime::query(), 'date');
        $amount['request_change_timesheet_app'] = $this->totalRCTByEmployee($employee_id, $year, $month);
        $amount['total_application'] = $amount['leave_app'] + $amount['over_time_app'] + $amount['request_change_timesheet_app'];

        $amount['all_leave_app'] = $this->totalLeaveFormManagement($request->month);
        $amount['all_compensatory_leave_app'] = $this->totalCompensatoryFormManagement($request->month);
        $amount['all_over_time_app'] = $this->totalOverTimeManagement($request->month);
        $amount['all_request_change_timesheet_app'] = $this->totalRCTManagement($request->month);
        $amount['all_total_application'] = $amount['all_leave_app'] + $amount['all_over_time_app'] + $amount['all_request_change_timesheet_app'];
        $amount['all_total_labor_contract'] = $this->laborContractInterface->checkExpiringContract(null)->count();
        $amount['expired_contract'] = $this->laborContractInterface->checkExpiringContract($expire)->count();
        $amount['almost_expired_contract'] = $this->laborContractInterface->checkExpiringContract($active)->count();

        return response()->json($amount);
    }

    /**
     * @param $employee_id
     * @param $year
     * @param $month
     * @param $field
     * @return mixed
     */
    public function sumTimeSheetByField($employee_id, $year, $month, $field)
    {
        return TimeSheet::where(['employee_id' => $employee_id])
            ->whereYear('date', '=', $year)
            ->whereMonth('date', '=', $month)
            ->sum($field);
    }

    /**
     * @param $employee_id
     * @param $year
     * @param $month
     * @param $field
     * @return mixed
     */
    public function sumOverTimeByField($employee_id, $year, $month, $field)
    {
        return OverTime::where([['status', OverTime::STATUS['APPROVED']], ['employee_id', '=', $employee_id]])
            ->whereYear('date', '=', $year)
            ->whereMonth('date', '=', $month)
            ->sum($field);
    }

    /**
     * @param $employee_id
     * @param $year
     * @param $month
     * @param $query
     * @return mixed
     */
    public function totalFormByEmployee($employee_id, $year, $month, $query, $field = 'start_time')
    {
        return $query->where(['employee_id' => $employee_id])
            ->whereYear($field, '=', $year)
            ->whereMonth($field, '=', $month)
            ->count();
    }

    /**
     * @param $employee_id
     * @param $year
     * @param $month
     * @return int
     */
    public function totalRCTByEmployee($employee_id, $year, $month)
    {
        return RequestChangeTimesheet::query()->where(['employee_id' => $employee_id])
            ->whereYear('date', '=', $year)
            ->whereMonth('date', '=', $month)
            ->count();
    }

    /**
     * @param $date
     * @return mixed
     */
    public function totalLeaveFormManagement($date)
    {
        $query = LeaveForm::query();

        return $this->leaveForm->queryFilter($query, $date, LeaveForm::KEY_SCREEN['AWAITING_CONFIRM'])->count();
    }

    public function totalCompensatoryFormManagement($date)
    {
        $query = CompensatoryLeave::query();

        return $this->compensatoryLeaveForm->queryFilter($query, $date, CompensatoryLeave::KEY_SCREEN['AWAITING_CONFIRM'])->count();
    }

    /**
     * @param $date
     * @return mixed
     */
    public function totalOverTimeManagement($date)
    {
        $query = OverTime::query();

        return $this->overtime->queryFilter($query, $date, OverTime::KEY_SCREEN['AWAITING_CONFIRM'])->count();
    }

    /**
     * @param $date
     * @return mixed
     */
    public function totalRCTManagement($date)
    {
        $query = RequestChangeTimesheet::query();

        return $this->requestChangeTimesheet->queryFilter($query, $date, RequestChangeTimesheet::KEY_SCREEN['AWAITING_CONFIRM'])->count();
    }
}
