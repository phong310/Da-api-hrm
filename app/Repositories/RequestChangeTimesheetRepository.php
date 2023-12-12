<?php

namespace App\Repositories;

use App\Models\Form\LeaveForm;
use App\Models\Form\RequestChangeTimesheet;
use App\Models\TimeSheet\TimeSheetsLog;
use App\Repositories\Interfaces\Forms\RequestChangeTimesheetInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RequestChangeTimesheetRepository implements RequestChangeTimesheetInterface
{
    /**
     * @var RequestChangeTimesheet
     */
    protected $request_change_timesheet;

    /**
     * @param RequestChangeTimesheet $request_change_timesheet
     */
    public function __construct(RequestChangeTimesheet $request_change_timesheet)
    {
        $this->request_change_timesheet = $request_change_timesheet;
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|object|null\
     */
    public function show($id)
    {
        return $this->request_change_timesheet::query()->where(['id' => $id])->first();
    }

    /**
     * @param $date
     * @param $employee_id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function showByDate($date, $employee_id)
    {
        return $this->request_change_timesheet::query()
            ->whereNotIn('status', [RequestChangeTimesheet::STATUS['REJECTED'], RequestChangeTimesheet::STATUS['CANCEL']])
            ->where(['date' => $date, 'employee_id' => $employee_id])->first();
    }

    /**
     * @param $formId
     * @return bool
     */
    public function checkIsApprover($formId)
    {
        $employeeId = Auth::user()->employee_id;
        $query = $this->request_change_timesheet::query();

        $query->where(['id' => $formId])
            ->whereHas('approvers', function ($q) use ($employeeId) {
                $q->where(['approve_employee_id' => $employeeId]);
            });

        if ($query->exists()) {
            return true;
        }

        return false;
    }

    public function queryFilter($query, $date, $screenKey)
    {
        if ($screenKey == RequestChangeTimesheet::KEY_SCREEN['AWAITING_CONFIRM']) {
            $query->whereHas('approvers', function ($query) {
                $query->where(['approve_employee_id' => Auth::user()->employee_id]);
                $query->where('model_has_approvers.status', '=', RequestChangeTimesheet::STATUS['PROCESSING']);
            })
                ->where('requests_change_timesheets.status', '=', RequestChangeTimesheet::STATUS['PROCESSING']);
        } else {
            $query->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('approvers', function ($q) {
                        $q->where(['approve_employee_id' => Auth::user()->employee_id]);
                    })->whereNotIn('requests_change_timesheets.status', [RequestChangeTimesheet::STATUS['CANCEL'], RequestChangeTimesheet::STATUS['PROCESSING']]);
                })
                    ->orWhere(function ($q) {
                        $q->whereHas('approvers', function ($query) {
                            $query->where(['approve_employee_id' => Auth::user()->employee_id])
                                ->where('model_has_approvers.status', '!=', RequestChangeTimesheet::STATUS['PROCESSING']);
                        })->whereNotIn('requests_change_timesheets.status', [RequestChangeTimesheet::STATUS['CANCEL']]);
                    });
            });
        }

        $today = Carbon::now();
        $year = $today->year;
        $month = $today->month;
        if ($date) {
            $yearMonth = explode('-', $date);
            $month = $yearMonth[1];
            $year = $yearMonth[0];
            $query
                ->whereYear('requests_change_timesheets.date', '=', $year)
                ->whereMonth('requests_change_timesheets.date', '=', $month);
        }


        $query->orderBy('requests_change_timesheets.created_at', 'DESC');

        return $query;
    }
}
