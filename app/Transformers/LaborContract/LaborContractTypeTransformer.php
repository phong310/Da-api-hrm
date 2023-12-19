<?php

namespace App\Transformers\LaborContract;

use App\Models\LaborContract\LaborContractType;
use League\Fractal\TransformerAbstract;

class LaborContractTypeTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(LaborContractType $laborContractType)
    {
        $data = [
            'id' => $laborContractType['id'],
            'name' => $laborContractType['name'],
            'duration_of_contract' => $laborContractType['duration_of_contract'],
            'status_apply_holiday' => $laborContractType['status_apply_holiday']
        ];

        if (count($laborContractType['allowances'])) {
            foreach ($laborContractType['allowances'] as $allowance) {
                $data['allowances'][] = [
                    "id" => $allowance['id'],
                    "name" => $allowance['name'],
                    "status" => $allowance['status'],
                    "amount_of_money" => $allowance['amount_of_money'],
                ];
            }
        } else {
            $data['allowances'] = null;
        }

        return $data;
    }
}
