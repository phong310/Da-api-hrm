<?php

namespace App\Transformers;

use App\Models\TimeSheet\TimeSheetsLog;
use League\Fractal\TransformerAbstract;

class TimeSheetLogTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(TimeSheetsLog $tsl)
    {
        return [
            'id' => $tsl->id,
            'employee_id' => $tsl->employee_id,
            'date_time' => $tsl->date_time,
            'type' => $tsl->type,
            'note' => $tsl->note,
        ];
    }
}
