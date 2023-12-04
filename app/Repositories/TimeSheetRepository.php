<?php

namespace App\Repositories;

use App\Models\TimeSheet\TimeSheet;
use App\Repositories\Interfaces\TimeSheetInterface;
use Carbon\Carbon;

class TimeSheetRepository implements TimeSheetInterface
{
    /**
     * @var TimeSheet
     */
    protected $timeSheet;

    /**
     * @param TimeSheet $timeSheet
     */
    public function __construct(TimeSheet $timeSheet)
    {
        $this->timeSheet = $timeSheet;
    }

    public function getTimesheetInMonthOfEmployee($date, $employeeId)
    {
        $today = Carbon::now();
        $month = $today->month;
        $year = $today->year;
        if ($date) {
            $dataArray = explode('-', $date);
            $month = $dataArray[1];
            $year = $dataArray[0];
        }

        return $this->timeSheet->query()
            ->where('employee_id', $employeeId)
            ->whereYear('date', '=', $year)
            ->whereMonth('date', '=', $month)
            ->with([
                'overtime.overtimeSalaryCoefficients',
                'leaveFormHasTimesheets.leaveForm.kind_of_leave',
                'compensatoryLeaveHasTimesheet.compensatoryLeave.kind_of_leave',
            ])
            ->orderBy('date')
            ->get();
    }

    public function getTimesheetInDate($startDate, $endDate, $employeeId)
    {
        return $this->timeSheet->query()
            ->where('employee_id', $employeeId)
            ->whereBetween('date', [$startDate, $endDate])
            ->with([
                'overtime.overtimeSalaryCoefficients',
                'leaveFormHasTimesheets.leaveForm.kind_of_leave',
                'compensatoryLeaveHasTimesheet.compensatoryLeave.kind_of_leave',
            ])
            ->orderBy('date')
            ->get();
    }

    public function showTotalWorkByDate($date, $employeeId)
    {
        return $this->timeSheet::query()
            ->select('id', 'total_time_work')
            ->where(['date' => $date, 'employee_id' => $employeeId])
            ->first();
    }

    public function showOvertimeSalaryCoefficients($date, $employeeId)
    {
        return $this->timeSheet::query()
            ->where(['date' => $date, 'employee_id' => $employeeId])
            ->with(['overtime.overtimeSalaryCoefficients'])
            ->first();
    }
}
