<?php

namespace App\Transformers;

use App\Models\Master\KindOfLeave;
use League\Fractal\TransformerAbstract;

class KindOfLeaveTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(KindOfLeave $kol)
    {
        return [
            'id' => $kol->id,
            'name' => $kol->name,
            'symbol' => $kol->symbol,
            'type' => $kol->type,
        ];
    }
}
