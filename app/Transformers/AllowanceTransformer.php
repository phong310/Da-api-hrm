<?php

namespace App\Transformers;

use App\Models\LaborContract\Allowance;
use App\Models\Master\Holiday;
use League\Fractal\TransformerAbstract;

class AllowanceTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Allowance $allowance)
    {
        return [
            'id' => $allowance->id,
            'name' => $allowance->name,
            'status' => $allowance->status,
            'amount_of_money' => $allowance->amount_of_money
        ];
    }
}
