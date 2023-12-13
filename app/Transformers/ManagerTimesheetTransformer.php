<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class ManagerTimesheetTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($timesheets)
    {
        $timesheetsByDate = $timesheets->groupBy('date');

        $employeeTimesheets = null;
        foreach ($timesheetsByDate as $key => $timesheets) {
            $arrayTimesheets = null;
            foreach ($timesheets as $timesheet) {
                $arrayTimesheets[] = [
                    'id' => $timesheet->id,
                    'start_time' => $timesheet->start_time,
                    'end_time' => $timesheet->end_time,
                    'type' => $timesheet->type,
                ];
            }
            $employeeTimesheets[$key] = $arrayTimesheets;
        }

        return [
            'timesheets' => $employeeTimesheets,
            'employee_id' => $timesheets[0]->employee_id,
            'job_position' => $timesheets[0]->employee->jobPosition->name,
            'department' => $timesheets[0]->employee->workingDepartment->name,
            'fullname' => $timesheets[0]->employee->personalInformation->fullname,
        ];
    }
}
