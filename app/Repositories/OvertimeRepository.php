<?php

namespace App\Repositories;

use App\Models\Form\LeaveForm;
use App\Models\Form\OverTime;
use App\Repositories\Interfaces\Forms\OvertimeInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OvertimeRepository implements OvertimeInterface
{
    /**
     * @var OverTime
     */
    protected $overtime;

    /**
     * @param OverTime $overtime
     */
    public function __construct(OverTime $overtime)
    {
        $this->overtime = $overtime;
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|object|null\
     */
    public function show($id)
    {
        return $this->overtime::query()->where(['id' => $id])->first();
    }

    public function store($data)
    {
        return $this->overtime::query()->create($data);
    }

    /**
     * @param $date
     * @param $employee_id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function showByDate($date, $employee_id)
    {
        return $this->overtime::query()
            ->whereNotIn('status', [OverTime::STATUS['REJECTED'], OverTime::STATUS['CANCEL']])
            ->where(['date' => $date, 'employee_id' => $employee_id])->first();
    }

    /**
     * @param $formId
     * @return bool
     */
    public function checkIsApprover($formId)
    {
        $employeeId = Auth::user()->employee_id;
        $query = $this->overtime::query();

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
        if ($screenKey == OverTime::KEY_SCREEN['AWAITING_CONFIRM']) {
            $query->whereHas('approvers', function ($query) {
                $query->where(['approve_employee_id' => Auth::user()->employee_id]);
                $query->where('model_has_approvers.status', '=', OverTime::STATUS['PROCESSING']);
            })
                ->where('over_times.status', '=', OverTime::STATUS['PROCESSING']);
        } else {
            $query->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('approvers', function ($q) {
                        $q->where(['approve_employee_id' => Auth::user()->employee_id]);
                    })->whereNotIn('over_times.status', [OverTime::STATUS['CANCEL'], OverTime::STATUS['PROCESSING']]);
                })
                    ->orWhere(function ($q) {
                        $q->whereHas('approvers', function ($query) {
                            $query->where(['approve_employee_id' => Auth::user()->employee_id])
                                ->where('model_has_approvers.status', '!=', OverTime::STATUS['PROCESSING']);
                        })->whereNotIn('over_times.status', [OverTime::STATUS['CANCEL']]);
                    });
            });
        }

        if ($date) {
            $yearMonth = explode('-', $date);
            $month = $yearMonth[1];
            $year = $yearMonth[0];
            $query->whereYear('over_times.date', '=', $year)
                ->whereMonth('over_times.date', '=', $month);
        }

        $query->orderBy('over_times.created_at', 'DESC');

        return $query;
    }
}
