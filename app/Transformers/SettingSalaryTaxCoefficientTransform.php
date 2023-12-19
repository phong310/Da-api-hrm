<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class SettingSalaryTaxCoefficientTransform extends TransformerAbstract
{
    public function transform($settingSalaryTaxCoefficient): array
    {
        return [
            'id' => $settingSalaryTaxCoefficient->id,
            'company_id' => $settingSalaryTaxCoefficient->company_id,
            'currency' => $settingSalaryTaxCoefficient->currency,
            'amount_money_syndicate' => $settingSalaryTaxCoefficient->amount_money_syndicate,
            'percent_social_insurance' => $settingSalaryTaxCoefficient->percent_social_insurance,
            'percent_medical_insurance' => $settingSalaryTaxCoefficient->percent_medical_insurance,
            'percent_unemployment_insurance' => $settingSalaryTaxCoefficient->percent_unemployment_insurance,
            'reduce_yourself' => $settingSalaryTaxCoefficient->reduce_yourself,
            'family_allowances' => $settingSalaryTaxCoefficient->family_allowances,
            'insurance_salary' => $settingSalaryTaxCoefficient->insurance_salary,
            'percent_syndicate' => $settingSalaryTaxCoefficient->percent_syndicate,
        ];
    }
}
