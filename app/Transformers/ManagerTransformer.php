<?php

namespace App\Transformers;

use App\Traits\SystemSetting;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class ManagerTransformer extends TransformerAbstract
{
    use SystemSetting;

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($manager)
    {
        $tableName = $manager->getTable();

        $statusModelHasApprover = 0;
        foreach ($manager->approvers as $approver) {
            $employeeId = Auth::user()->employee_id;
            if ($approver->approve_employee_id === $employeeId) {
                $statusModelHasApprover = $approver->status;
            }
        }

        if ($tableName === 'leave_form') {
            $data = [
                'id' => $manager->id,
                'employee_id' => $manager->employee_id,
                'kind_of_leave' => $manager->kind_of_leave,
                'created_at' => $this->convertTZToDateTime($manager->created_at),
                'start_time' => $manager->start_time,
                'end_time' => $manager->end_time,
                'card_number' => $manager->employee->card_number ?? null,
                'employee_name' => $manager->employee->information->full_name ?? null,
                'employee_code' => $manager->employee->employee_code ?? null,
                'thumbnail_url' => $manager->employee->information->thumbnail_url ?? null,
                'job_position' => $manager->employee->position->name ?? null,
                'status' => $manager->status,
                'status_model_has_approve' =>  $statusModelHasApprover,
                'is_salary' => $manager->is_salary,
            ];


            if ($data['kind_of_leave']) {
                $kolName = $data['kind_of_leave']['name'];
                unset($data['kind_of_leave']);
                $data['kind_of_leave']['name'] = $kolName;
            }

            return $data;
        }

        if ($tableName === 'over_times') {
            return [
                'id' => $manager->id,
                'employee_id' => $manager->employee_id,
                'created_at' => $this->convertTZToDateTime($manager->created_at),
                'start_time' => $manager->start_time,
                'end_time' => $manager->end_time,
                'card_number' => $manager->employee->card_number ?? null,
                'employee_name' => $manager->employee->information->full_name ?? null,
                'thumbnail_url' => $manager->employee->information->thumbnail_url ?? null,
                'status' => $manager->status,
                'total_time_work' => $manager->total_time_work,
                'status_model_has_approve' =>  $statusModelHasApprover,
                'date' => $manager->date,
            ];
        }

        if ($tableName === 'requests_change_timesheets') {
            return [
                'id' => $manager->id,
                'employee_id' => $manager->employee_id,
                'created_at' => $this->convertTZToDateTime($manager->created_at),
                'check_in_time' => $manager->check_in_time,
                'check_out_time' => $manager->check_out_time,
                'note' => $manager->note,
                'card_number' => $manager->employee->card_number ?? null,
                'employee_name' => $manager->employee->information->full_name ?? null,
                'thumbnail_url' => $manager->employee->information->thumbnail_url ?? null,
                'date' => $manager->date,
                'status' => $manager->status,
                'status_model_has_approve' =>  $statusModelHasApprover,
            ];
        }

        if ($tableName === 'compensatory_leaves') {
            $data = [
                'id' => $manager->id,
                'employee_id' => $manager->employee_id,
                'kind_of_leave' => $manager->kind_of_leave,
                'created_at' => $this->convertTZToDateTime($manager->created_at),
                'start_time' => $manager->start_time,
                'end_time' => $manager->end_time,
                'card_number' => $manager->employee->card_number ?? null,
                'employee_name' => $manager->employee->information->full_name ?? null,
                'thumbnail_url' => $manager->employee->information->thumbnail_url ?? null,
                'status' => $manager->status,
                'status_model_has_approve' =>  $statusModelHasApprover,
            ];

            if ($data['kind_of_leave']) {
                $kolName = $data['kind_of_leave']['name'];
                unset($data['kind_of_leave']);
                $data['kind_of_leave']['name'] = $kolName;
            }

            return $data;
        }
    }
}
