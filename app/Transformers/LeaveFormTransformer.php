<?php

namespace App\Transformers;

use App\Models\Form\LeaveForm;
use App\Traits\SystemSetting;
use Illuminate\Support\Arr;
use League\Fractal\TransformerAbstract;

class LeaveFormTransformer extends TransformerAbstract
{
    use SystemSetting;

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(LeaveForm $leaveForm)
    {
        $data = [
            'id' => $leaveForm->id,
            'created_at' => $this->convertTZToDateTime($leaveForm->created_at),
            'start_time' => $leaveForm->start_time,
            'end_time' => $leaveForm->end_time,
            'note' => $leaveForm->note,
            'reason' => $leaveForm->reason,
            'status' => $leaveForm->status,
            //            'kind_of_leave' => Arr::only( $leaveForm->kind_of_leave, ['id', 'name']),
            'kind_of_leave' => $leaveForm->kind_of_leave,
            //            'employee' => $leaveForm->employee,
            'is_salary' => $leaveForm->is_salary,
            'total_time_off' => $leaveForm->total_time_off
        ];
        if ($data['kind_of_leave']) {
            $kolName = $data['kind_of_leave']['name'];
            unset($data['kind_of_leave']);
            $data['kind_of_leave']['name'] = $kolName;
        }

        foreach ($leaveForm->approvers as $index => $approver) {
            if ($approver->employee && $approver->employee->information) {
                $data['approver_' . ($index + 1)] = $approver->employee->information->full_name;
            }
        }

        return $data;
    }
}
