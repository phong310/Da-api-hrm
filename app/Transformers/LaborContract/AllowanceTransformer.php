<?php

namespace App\Transformers\LaborContract;

use App\Models\LaborContract\Allowance;
use App\Models\LaborContract\LaborContractType;
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
        $data = [
            "id" => $allowance['id'],
            "name" => $allowance['name'],
            "status" => $allowance['status'],
            "amount_of_money" => $allowance['amount_of_money'],
        ];

        return $data;
    }
}
