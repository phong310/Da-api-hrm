<?php

namespace App\Repositories;

use App\Models\Form\LeaveForm;
use App\Repositories\Interfaces\Forms\LeaveFormInterface;
use Illuminate\Support\Facades\Auth;

class LeaveFormRepository implements LeaveFormInterface
{
    /**
     * @var LeaveForm
     */
    protected $leave_form;

    /**
     * @param LeaveForm $leave_form
     */
    public function __construct(LeaveForm $leave_form)
    {
        $this->leave_form = $leave_form;
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|object|null\
     */
    public function show($id)
    {
        return $this->leave_form::query()->where(['id' => $id])->first();
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|object|null\
     */
    public function showByEmployee($id)
    {
        $employeeId = Auth::user()->employee_id;

        return $this->leave_form::query()->where(['id' => $id, 'employee_id' => $employeeId])->first();
    }

    /**
     * @param $date
     * @param $employee_id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function showByDate($date, $employee_id)
    {
        return $this->leave_form::query()
            ->where(['employee_id' => $employee_id])
            ->whereNotIn('status', [LeaveForm::STATUS['REJECTED'], LeaveForm::STATUS['CANCEL']])
            ->whereDate('start_time', '<=', $date)
            ->whereDate('end_time', '>=', $date)
            ->get();
    }

    /**
     * @param $formId
     * @return bool
     */
    public function checkIsApprover($formId)
    {
        $employeeId = Auth::user()->employee_id;
        $query = $this->leave_form::query();

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
        if ($screenKey == LeaveForm::KEY_SCREEN['AWAITING_CONFIRM']) {
            $query->whereHas('approvers', function ($query) {
                $query->where(['approve_employee_id' => Auth::user()->employee_id]);
                $query->where('model_has_approvers.status', '=', LeaveForm::STATUS['PROCESSING']);
            })
                ->where('leave_form.status', '=', LeaveForm::STATUS['PROCESSING']);
        } else {
            $query->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('approvers', function ($q) {
                        $q->where(['approve_employee_id' => Auth::user()->employee_id]);
                    })->whereNotIn('leave_form.status', [LeaveForm::STATUS['CANCEL'], LeaveForm::STATUS['PROCESSING']]);
                })
                    ->orWhere(function ($q) {
                        $q->whereHas('approvers', function ($query) {
                            $query->where(['approve_employee_id' => Auth::user()->employee_id])
                                ->where('model_has_approvers.status', '!=', LeaveForm::STATUS['PROCESSING']);
                        })->whereNotIn('leave_form.status', [LeaveForm::STATUS['CANCEL']]);
                    });
            });
        }

        if ($date) {
            $yearMonth = explode('-', $date);
            $year = $yearMonth[0];
            $month = $yearMonth[1];

            $query->whereYear('leave_form.start_time', '=', $year)
                ->whereMonth('leave_form.start_time', '=', $month);
        }

        $query->orderBy('leave_form.created_at', 'DESC');

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

        $leaveForm = LeaveForm::query()
            ->where(['employee_id' => $employee_id, 'company_id' => $company_id])
            ->whereNotIn('status', [LeaveForm::STATUS['REJECTED'], LeaveForm::STATUS['CANCEL']])
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($query) use ($startTime, $endTime) {
                    $query->where(function ($query) use ($startTime, $endTime) {
                        $query->whereBetween('start_time', [$startTime, $endTime])
                            ->whereBetween('end_time', [$startTime, $endTime]);
                    })
                        ->orWhere(function ($query) use ($endTime) {
                            $query->whereDate('start_time', '=', $endTime)
                                ->whereTime('end_time', '>', '00:00:00');
                        });
                })
                    ->orWhere(function ($query) use ($startTime, $endTime) {
                        $query->whereDate('start_time', '<=', $startTime)
                            ->whereDate('end_time', '>=', $endTime);
                    });
            });

        if ($id) {
            $leaveForm->where('id', '!=', $id);
        }

        if ($leaveForm->exists()) {
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

        $query = LeaveForm::query()
            ->where(['employee_id' => $employee_id, 'company_id' => $company_id])
            ->whereNotIn('status', [LeaveForm::STATUS['REJECTED'], LeaveForm::STATUS['CANCEL']])
            ->whereDate('start_time', '<=', $date)
            ->whereDate('end_time', '>=', $date);

        if ($id) {
            $query->where('id', '!=', $id);
        }

        return $query;
    }

    public function queryFormHasProcessing($employee_id)
    {
        $user = Auth::user();
        $company_id = $user->company_id;

        return LeaveForm::query()
            ->where(['employee_id' => $employee_id, 'company_id' => $company_id])
            ->where('status', '=', LeaveForm::STATUS['PROCESSING'])
            ->where('is_salary', '=', LeaveForm::PAID_LEAVE['YES'])
            ->get();
    }

    public function getLeaveFormExit($id, $companyId)
    {
        return LeaveForm::query()
            ->where('company_id', $companyId)
            ->where('kind_leave_id', $id)
            ->exists();
    }
}
