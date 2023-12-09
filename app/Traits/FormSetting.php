<?php

namespace App\Traits;

trait FormSetting
{
    public function customDataApproval($data)
    {
        $approvers = [];
        foreach ($data->approvers as $index => $approver) {
            $fieldName = 'approver_id_' . ($index + 1);
            $data->$fieldName = $approver->approve_employee_id;
            $approvers[$index]['status'] = $approver->status;
            $approvers[$index]['employee_code'] = $approver->employee ? $approver->employee->employee_code : null;
            $approvers[$index]['avatar'] =  $approver->employee ? $approver->employee->information->thumbnail_url : null;
            $approvers[$index]['full_name'] = $approver->employee ? $approver->employee->information->full_name : null;
            $approvers[$index]['approval_time'] = $approver->approval_time;
            $approvers[$index]['rejected_time'] = $approver->rejected_time;
            $approvers[$index]['position'] = $approver->employee ? $approver->employee->position : null;
        }
        unset($data->approvers);
        $data->approvers = $approvers;

        return $data;
    }
}
