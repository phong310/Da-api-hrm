<?php

namespace App\Transformers\LaborContract;

use App\Models\LaborContract\LaborContract;
use League\Fractal\TransformerAbstract;

class LaborContractAllowanceTransForm extends TransformerAbstract
{
    public function transformData(LaborContract $laborContract)
    {
        $transformedData = $laborContract->toArray();
        $allowances = [];

        foreach ($transformedData['allowances'] as $allowance) {
            $allowances[] = [
                'id' => $allowance['allowance']['id'],
                'name' => $allowance['allowance']['name'],
                'status' => $allowance['allowance']['status'],
                'amount_of_money' => $allowance['allowance']['amount_of_money'],
                'company_id' => $allowance['allowance']['company_id'],
            ];
        }
        $transformedData['allowances'] = $allowances;
        unset($transformedData['created_at']);
        unset($transformedData['updated_at']);

        return $transformedData;
    }
}
