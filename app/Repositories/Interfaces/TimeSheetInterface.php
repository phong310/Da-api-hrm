<?php

namespace App\Repositories\Interfaces;

interface TimeSheetInterface extends BaseInterface
{
    /**
     * @return mixed
     */
    public function getTimesheetInMonthOfEmployee($month, $employeeId);

    public function getTimesheetInDate($startDate, $endDate, $employeeId);

    public function showTotalWorkByDate($date, $employeeId);

    public function showOvertimeSalaryCoefficients($date, $employeeId);
}
