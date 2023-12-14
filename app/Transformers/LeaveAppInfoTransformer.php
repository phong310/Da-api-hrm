<?php

namespace App\Transformers;

use App\Models\Form\LeaveForm;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class LeaveAppInfoTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(LeaveForm $leaveForm)
    {
        $in_day = Carbon::parse($leaveForm->start_time)->toDateString() === Carbon::parse($leaveForm->end_time)->toDateString();
        return [
            'id' => $leaveForm->id,
            'start_time' => $leaveForm->start_time,
            'end_time' => $leaveForm->end_time,
            'created_at' => $leaveForm->created_at,
            'kind_leave_id' => $leaveForm->kind_leave_id,
            'full_name' => $leaveForm->employee->personalInformation->full_name ?? null,
            'thumbnail_url' => $leaveForm->employee->personalInformation->thumbnail_url ?? null,
            'in_day' => $in_day,
            'kind_of_leave' => $leaveForm->kind_of_leave->name ?? null,
            'status' => $leaveForm->status
        ];
    }
}
