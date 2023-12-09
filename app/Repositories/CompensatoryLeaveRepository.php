<?php

namespace App\Repositories;

use App\Models\Form\CompensatoryLeave;
use App\Models\Form\LeaveForm;
use App\Repositories\Interfaces\Forms\CompensatoryLeaveInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CompensatoryLeaveRepository implements CompensatoryLeaveInterface
{

    /**
     * @var CompensatoryLeave
     */
    private $compensatoryLeave;

    /**
     * @param CompensatoryLeave $compensatoryLeave
     */
    public function __construct(CompensatoryLeave $compensatoryLeave)
    {
        $this->compensatoryLeave = $compensatoryLeave;
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|Model|mixed|object|null
     */
    public function show($id)
    {
        return $this->compensatoryLeave::query()->where(['id' => $id])->first();
    }

    public function store($data)
    {
        return $this->compensatoryLeave::query()->create($data);
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|Model|mixed|object|null
     */
    public function showByEmployee($id)
    {
        $employeeId = Auth::user()->employee_id;

        return $this->compensatoryLeave::query()->where(['id' => $id, 'employee_id' => $employeeId])->first();
    }

    /**
     * @param $date
     * @param $employee_id
     * @return mixed
     */
    public function showByDate($date, $employee_id)
    {
        return $this->compensatoryLeave::query()
            ->where(['employee_id' => $employee_id])
            ->whereNotIn('status', [CompensatoryLeave::STATUS['REJECTED'], CompensatoryLeave::STATUS['CANCEL']])
            ->whereDate('start_time', '<=', $date)
            ->whereDate('end_time', '>=', $date)
            ->first();
    }

    /**
     * @param $formId
     * @return bool
     */
    public function checkIsApprover($formId)
    {
        $employeeId = Auth::user()->employee_id;
        $query = $this->compensatoryLeave::query();

        $query->where(['id' => $formId])
            ->whereHas('approvers', function ($q) use ($employeeId) {
                $q->where(['approve_employee_id' => $employeeId]);
            });

        if ($query->exists()) {
            return true;
        }

        return false;
    }

    /**
     * @param $query
     * @param $date
     * @param $screenKey
     * @return mixed
     */
    public function queryFilter($query, $date, $screenKey)
    {
        if ($screenKey == CompensatoryLeave::KEY_SCREEN['AWAITING_CONFIRM']) {
            $query->whereHas('approvers', function ($query) {
                $query->where(['approve_employee_id' => Auth::user()->employee_id]);
                $query->where('model_has_approvers.status', '=', CompensatoryLeave::STATUS['PROCESSING']);
            })
                ->where('compensatory_leaves.status', '=', CompensatoryLeave::STATUS['PROCESSING']);
        } else {
            $query->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('approvers', function ($q) {
                        $q->where(['approve_employee_id' => Auth::user()->employee_id]);
                    })->whereNotIn('compensatory_leaves.status', [CompensatoryLeave::STATUS['CANCEL'], CompensatoryLeave::STATUS['PROCESSING']]);
                })
                    ->orWhere(function ($q) {
                        $q->whereHas('approvers', function ($query) {
                            $query->where(['approve_employee_id' => Auth::user()->employee_id])
                                ->where('model_has_approvers.status', '!=', CompensatoryLeave::STATUS['PROCESSING']);
                        })->whereNotIn('compensatory_leaves.status', [CompensatoryLeave::STATUS['CANCEL']]);
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
                ->whereYear('compensatory_leaves.start_time', '=', $year)
                ->whereMonth('compensatory_leaves.start_time', '=', $month);
        }


        $query->orderBy('compensatory_leaves.created_at', 'DESC');

        return $query;
    }

    /**
     * @param $startTime
     * @param $endTime
     * @param $id
     * @return bool
     */
    public function checkFormIsExistByStartEndTime($startTime, $endTime, $id)
    {
        $user = Auth::user();
        $company_id = $user->company_id;
        $employee_id = $user->employee_id;

        $compensatoryLeave = CompensatoryLeave::query()
            ->where(['employee_id' => $employee_id, 'company_id' => $company_id])
            ->whereNotIn('status', [CompensatoryLeave::STATUS['REJECTED'], CompensatoryLeave::STATUS['CANCEL']])
            ->where(['start_time' => $startTime, 'end_time' => $endTime]);

        if ($id) {
            $compensatoryLeave->where('id', '!=', $id);
        }

        if ($compensatoryLeave->exists()) {
            return true;
        }

        return false;
    }

    /**
     * @param $date
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|mixed
     */
    public function queryFormByDateExceptId($date, $id)
    {
        $user = Auth::user();
        $company_id = $user->company_id;
        $employee_id = $user->employee_id;

        $query = CompensatoryLeave::query()
            ->where(['employee_id' => $employee_id, 'company_id' => $company_id])
            ->whereNotIn('status', [LeaveForm::STATUS['REJECTED'], LeaveForm::STATUS['CANCEL']])
            ->whereDate('start_time', '<=', $date)
            ->whereDate('end_time', '>=', $date);

        if ($id) {
            $query->where('id', '!=', $id);
        }

        return $query;
    }

    public function getCompensatoryLeave($id, $companyId)
    {
        return CompensatoryLeave::query()
            ->where('company_id', $companyId)
            ->where('kind_leave_id', $id)
            ->exists();
    }
}
