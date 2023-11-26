<?php

namespace App\Transformers;

use App\Models\Company;
use League\Fractal\TransformerAbstract;

class CompanyTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Company $company)
    {
        return [
            'id' => $company->id,
            'name' => $company->name,
            'phone_number' => $company->phone_number,
            'address' => $company->address,
            'status' => $company->status,
            'tax_code' => $company->tax_code,
            'start_time' => $company->start_time,
            'end_time' => $company->end_time,
            'logo' => $company->logo,
            'representative' => $company->representative,
            'type_of_business' => $company->type_of_business,
            'register_date' => $company->register_date,
        ];
    }
}
