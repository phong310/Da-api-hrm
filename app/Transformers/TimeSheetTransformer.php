<?php

namespace App\Transformers;

use App\Models\TimeSheet\TimeSheet;
use League\Fractal\TransformerAbstract;

class TimeSheetTransformer extends TransformerAbstract
{
    private $minusTotalTimeWorking;

    public function __construct($minusTotalTimeWorking)
    {
        $this->minusTotalTimeWorking = $minusTotalTimeWorking;
    }

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(TimeSheet $timesheet)
    {
        $data = [
            'id' => $timesheet['id'],
            'employee_id' => $timesheet['employee_id'],
            'start_time' => $timesheet['start_time'],
            'end_time' => $timesheet['end_time'],
            'date' => $timesheet['date'],
            'total_time_work' => $timesheet['total_time_work'],
            'total_time_work_without_time_off' => $timesheet['total_time_work'],
            'late_time' => $timesheet['late_time'],
            'time_early' => $timesheet['time_early'],
            'type' => $timesheet['type'],
            'total_all_time_work' => $timesheet['total_all_time_work']
        ];

        $overtime = $timesheet['overtime'];
        if ($overtime) {
            $data['overtime'] = [
                'id' => $overtime['id'],
                'employee_id' => $overtime['employee_id'],
                'start_time' => $overtime['start_time'],
                'end_time' => $overtime['end_time'],
                'date' => $overtime['date'],
                'timesheet_id' => $overtime['timesheet_id'],
                'company_id' => $overtime['company_id'],
                'status' => $overtime['status'],
                'total_time_work' => $overtime['total_time_work'],
                // 'coefficient_salary' => $overtime['coefficient_salary'],
            ];

            $overtimeSalaryCoefficients = $overtime['overtimeSalaryCoefficients'];
            if (count($overtimeSalaryCoefficients)) {
                foreach ($overtimeSalaryCoefficients as $overtimeSalaryCoefficient) {
                    unset($overtimeSalaryCoefficient['created_at']);
                    unset($overtimeSalaryCoefficient['updated_at']);
                }
                $data['overtime']['overtime_salary_coefficients'] = $overtimeSalaryCoefficients;
            }
        } else {
            $data['overtime'] = null;
        }

        $lfHasTimesheets = $timesheet['leaveFormHasTimesheets'];
        if (count($lfHasTimesheets)) {
            foreach ($lfHasTimesheets as $lfHasTimesheet) {
                $data['leave_form_has_timesheets'][] = [
                    'id' => $lfHasTimesheet['id'],
                    'timesheet_id' => $lfHasTimesheet['timesheet_id'],
                    'leave_form_id' => $lfHasTimesheet['leave_form_id'],
                    'start_time' => $lfHasTimesheet['start_time'],
                    'end_time' => $lfHasTimesheet['end_time'],
                    'date' => $lfHasTimesheet['date'],
                    'time_off' => $lfHasTimesheet['time_off'],
                    'leave_form' => [
                        'is_salary' => $lfHasTimesheet['leaveForm']->is_salary,
                        'kolSymbol' => $lfHasTimesheet['leaveForm']['kind_of_leave']->symbol ?? null
                    ]
                ];

                if ($lfHasTimesheet->leaveForm->is_salary) {
                    $data['total_time_work_without_time_off'] -= $lfHasTimesheet['time_off'];
                }

                if ($this->minusTotalTimeWorking && $lfHasTimesheet->leaveForm->is_salary) {
                    $data['total_time_work'] -= $lfHasTimesheet['time_off'];
                }
            }
        } else {
            $data['leave_form_has_timesheets'] = null;
        }

        $clHasTimesheet = $timesheet['compensatoryLeaveHasTimesheet'];
        if ($clHasTimesheet) {
            $data['compensatory_leave_has_timesheet'] = [
                'id' => $clHasTimesheet['id'],
                'timesheet_id' => $clHasTimesheet['timesheet_id'],
                'compensatory_leave_id' => $clHasTimesheet['compensatory_leave_id'],
                'start_time' => $clHasTimesheet['start_time'],
                'end_time' => $clHasTimesheet['end_time'],
                'date' => $clHasTimesheet['date'],
                'time_off' => $clHasTimesheet['time_off'],
                'compensatory_leave' => [
                    'kolSymbol' => $clHasTimesheet['CompensatoryLeave']['kind_of_leave']->symbol ?? null
                ]
            ];

            $data['total_time_work_without_time_off'] -= $clHasTimesheet['time_off'];

            if ($this->minusTotalTimeWorking) {
                $data['total_time_work'] -= $clHasTimesheet['time_off'];
            }
        } else {
            $data['compensatory_leave_has_timesheet'] = null;
        }

        return $data;
    }
}
