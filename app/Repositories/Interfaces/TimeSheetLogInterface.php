<?php

namespace App\Repositories\Interfaces;

interface TimeSheetLogInterface extends BaseInterface
{
    public function getTimeSheetLogOnDate($employee_id, $date);
}
