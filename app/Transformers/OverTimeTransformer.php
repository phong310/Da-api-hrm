<?php

namespace App\Transformers;

use App\Models\Form\OverTime;
use App\Traits\SystemSetting;
use League\Fractal\TransformerAbstract;

class OverTimeTransformer extends TransformerAbstract
{
    use SystemSetting;

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(OverTime $overtime)
    {
        $data = [
            'id' => $overtime->id,
            // 'employee_id' => $overtime->employee_id,
            'start_time' => $overtime->start_time,
            'end_time' => $overtime->end_time,
            'note' => $overtime->note,
            'reason' => $overtime->reason,
            'status' => $overtime->status,
            'total_time_work' => $overtime->total_time_work,
            'created_at' => $this->convertTZToDateTime($overtime->created_at),
            //            'employee' => $overtime->employee,
        ];
        foreach ($overtime->approvers as $index => $approver) {
            if ($approver->employee && $approver->employee->information) {
                $data['approver_' . ($index + 1)] = $approver->employee->information->full_name;
            }
        }

        return $data;
    }
}
