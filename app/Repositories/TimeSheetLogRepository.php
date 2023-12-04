<?php

namespace App\Repositories;

use App\Models\TimeSheet\TimeSheetsLog;
use App\Repositories\Interfaces\TimeSheetLogInterface;

class TimeSheetLogRepository implements TimeSheetLogInterface
{
    protected $timeSheetLog;
    public function __construct(TimeSheetsLog $timeSheetLog)
    {
        $this->timeSheetLog = $timeSheetLog;
    }

    public function getTimeSheetLogOnDate($employee_id, $date)
    {
        $times = [
            'first' => null,
            'last' => null,
        ];
        $first = TimeSheetsLog::query()
            ->where(['employee_id' => $employee_id])
            ->whereRaw("DATE(date_time) = '" . $date . "'")
            ->orderBy('date_time')
            ->first();

        $query = TimeSheetsLog::query()
            ->where(['employee_id' => $employee_id])
            ->whereRaw("DATE(date_time) = '" . $date . "'");

        if ($first) {
            $times['first'] = $first->date_time;
            $query->where('id', '!=', $first->id);
        }
        $last = $query->orderBy('date_time', 'DESC')->first();

        if ($last) {
            $times['last'] = $last->date_time;
        }

        return $times;
    }
}
