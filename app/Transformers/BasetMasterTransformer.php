<?php

namespace App\Transformers;

use App\Models\Master\BaseMaster;
use League\Fractal\TransformerAbstract;

class BaseMasterTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(BaseMaster $base)
    {
        return [
            'id' => $base->id,
            'name' => $base->name,
        ];
    }
}
