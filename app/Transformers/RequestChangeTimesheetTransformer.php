<?php

namespace App\Transformers;

use App\Models\Form\RequestChangeTimesheet;
use App\Traits\SystemSetting;
use League\Fractal\TransformerAbstract;

class RequestChangeTimesheetTransformer extends TransformerAbstract
{
    use SystemSetting;

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(RequestChangeTimesheet $requestChange)
    {
        $data = [
            'id' => $requestChange->id,
            //            'employee_id' => $requestChange->employee_id,
            'created_at' => $this->convertTZToDateTime($requestChange->created_at),
            'check_in_time' => $requestChange->check_in_time,
            'check_out_time' => $requestChange->check_out_time,
            // 'timesheet_id' => $requestChange->timesheet_id,
            'note' => $requestChange->note,
            'status' => $requestChange->status,
            // 'employee' => $requestChange->employee,
            // 'timesheet' => $requestChange->timesheet,
            'date' => $requestChange->date,
            'total_time' => $requestChange->total_time
        ];

        foreach ($requestChange->approvers as $index => $approver) {
            if ($approver->employee && $approver->employee->information) {
                $data['approver_' . ($index + 1)] = $approver->employee->information->full_name;
            }
        }

        return $data;
    }
}
