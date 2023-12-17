<?php

namespace App\Transformers;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use League\Fractal\TransformerAbstract;

class TimesheetLogManagerTransform extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($employee)
    {
        $timesheetLog = [];

        $timesheetLogsByDate = $employee['timesheetsLogs']->groupBy(function ($item) {
            return Carbon::parse($item->date_time)->format('Y-m-d');
        });

        foreach ($timesheetLogsByDate as $key => $value) {
            $arrDateTime = Arr::pluck($value, 'date_time');

            if (count($arrDateTime) == 1) {
                $timesheetLog[$key] = [
                    'start_time' => $arrDateTime[0],
                    'end_time' => null,
                ];
            } else {
                $timesheetLog[$key] = [
                    'start_time' => min($arrDateTime),
                    'end_time' => max($arrDateTime),
                ];
            }
        }

        return [
            'employee_id' => $employee->id,
            'job_position' => $employee->position->name ?? null,
            'department' => $employee->department->name ?? null,
            'employee_code' => $employee->employee_code ?? null,
            'fullname' => $employee->personalInformation->fullname ?? null,
            'avatar' => $employee->personalInformation->thumbnail_url ?? null,
            'timesheetLog' => $timesheetLog,
        ];
    }
}
