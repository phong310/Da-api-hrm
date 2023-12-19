<?php

namespace App\Transformers\LaborContract;

use App\Models\LaborContract\LaborContract;
use League\Fractal\TransformerAbstract;

class LaborContractTransformerMySelf extends TransformerAbstract
{
    public function transform(LaborContract $laborContract)
    {
        $data = $laborContract->toArray();
        $data['labor_contract_type'] = $laborContract->labor_contract_type;
        if ($data['employee']) {
            $employee = $data['employee'];
            $full_name = 'Unknown';
            if ($employee['personal_information']) {
                $full_name = $employee['personal_information']['full_name'];
            }
            unset($data['employee']);
            $data['employee']['employee_code'] = $employee['employee_code'];
            $data['employee']['personal_information']['full_name'] = $full_name;
        }
        return $data;
    }
}
