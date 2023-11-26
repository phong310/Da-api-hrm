<?php

namespace App\Transformers;

use App\Models\Master\WorkingDay;
use League\Fractal\TransformerAbstract;

class WorkingDayTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(WorkingDay $workingDay)
    {
        return [
            'id' => $workingDay->id,
            'name' => $workingDay->name,
            'type' => $workingDay->type,
            'start_time' => $this->convertTime($workingDay->start_time),
            'end_time' => $this->convertTime($workingDay->end_time),
            'start_lunch_break' => $this->convertTime($workingDay->start_lunch_break),
            'end_lunch_break' => $this->convertTime($workingDay->end_lunch_break),
            'day_in_week_id' => $workingDay->day_in_week_id,
            'day_in_week_name' => $workingDay->dayInWeek->name,
            'total_working_time' => $workingDay->total_working_time,
        ];
    }

    public function convertTime($value)
    {
        if ($value) {
            return substr($value, 0, 5);
        }

        return null;
    }
}
