<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

use App\Models\OtherAllowance;

class OtherAllowanceTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(OtherAllowance $allowance)
    {
        return [
            'id' => $allowance->id,
            'name' => $allowance->name,
            'company_id' => $allowance->company_id
        ];
    }
}
