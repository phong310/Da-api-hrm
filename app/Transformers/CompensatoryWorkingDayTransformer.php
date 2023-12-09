<?php

namespace App\Transformers;

use App\Models\Master\CompensatoryWorkingDay;
use League\Fractal\TransformerAbstract;

class CompensatoryWorkingDayTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(CompensatoryWorkingDay $compensatoryWorkingDay)
    {
        return [
            'id' => $compensatoryWorkingDay->id,
            'start_date' => $compensatoryWorkingDay->start_date,
            'end_date' => $compensatoryWorkingDay->end_date,
            'start_time' => $compensatoryWorkingDay->start_time,
            'end_time' => $compensatoryWorkingDay->end_time,
            'start_lunch_break' => $compensatoryWorkingDay->start_lunch_break,
            'end_lunch_break' => $compensatoryWorkingDay->end_lunch_break,
            'type' => $compensatoryWorkingDay->type,
            'name' => $compensatoryWorkingDay->name,
        ];
    }
}
