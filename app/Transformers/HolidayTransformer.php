<?php

namespace App\Transformers;

use App\Models\Master\Holiday;
use League\Fractal\TransformerAbstract;

class HolidayTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Holiday $holiday)
    {
        return [
            'id' => $holiday->id,
            'start_date' => $holiday->start_date,
            'end_date' => $holiday->end_date,
            'type' => $holiday->type,
            'name' => $holiday->name,
        ];
    }
}
