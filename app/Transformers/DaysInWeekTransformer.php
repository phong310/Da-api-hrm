<?php

namespace App\Transformers;

use App\Models\Master\DaysInWeek;
use League\Fractal\TransformerAbstract;

class DaysInWeekTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(DaysInWeek $diw)
    {
        return [
            'id' => $diw->id,
            'name' => $diw->name,
            'symbol' => $diw->symbol,
        ];
    }
}
