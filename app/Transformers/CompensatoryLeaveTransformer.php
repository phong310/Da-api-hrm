<?php

namespace App\Transformers;

use App\Models\Form\CompensatoryLeave;
use App\Traits\SystemSetting;
use League\Fractal\TransformerAbstract;

class CompensatoryLeaveTransformer extends TransformerAbstract
{
    use SystemSetting;

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(CompensatoryLeave $compensatoryLeave)
    {
        $data = [
            'id' => $compensatoryLeave->id,
            'created_at' => $this->convertTZToDateTime($compensatoryLeave->created_at),
            'start_time' => $compensatoryLeave->start_time,
            'end_time' => $compensatoryLeave->end_time,
            'status' => $compensatoryLeave->status,
            'reason' => $compensatoryLeave->reason,
            'note' => $compensatoryLeave->note,
            'kind_of_leave' => $compensatoryLeave->kind_of_leave,
            'total_time_off' => $compensatoryLeave->total_time_off
        ];

        if ($data['kind_of_leave']) {
            $kolName = $data['kind_of_leave']['name'];
            unset($data['kind_of_leave']);
            $data['kind_of_leave']['name'] = $kolName;
        }

        foreach ($compensatoryLeave->approvers as $index => $approver) {
            if ($approver->employee && $approver->employee->information) {
                $data['approver_' . ($index + 1)] = $approver->employee->information->full_name;
            }
        }

        return $data;
    }
}
