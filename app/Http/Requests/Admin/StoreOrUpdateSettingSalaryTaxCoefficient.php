<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrUpdateSettingSalaryTaxCoefficient extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        $rules = [
            'currency' => ['required'],
            'amount_money_syndicate',
            'percent_social_insurance' => ['required', 'numeric', 'gte:0', 'between:0,1000'],
            'percent_medical_insurance' => ['required', 'numeric', 'gte:0', 'between:0,1000'],
            'percent_unemployment_insurance' => ['required', 'numeric', 'gte:0', 'between:0,1000'],
            'reduce_yourself' => ['required'],
            'family_allowances' => ['required'],
            'insurance_salary' => ['required'],
            'percent_syndicate'
        ];
        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'currency' => __('attributes.currency'),
            'percent_social_insurance' => __('attributes.percent_social_insurance'),
            'percent_medical_insurance' => __('attributes.percent_medical_insurance'),
            'percent_unemployment_insurance' => __('attributes.percent_unemployment_insurance'),
            'reduce_yourself' =>  __('attributes.reduce_yourself'),
            'family_allowances' => __('attributes.family_allowances'),
            "insurance_salary" => __('attributes.insurance_salary')
        ];

        return $attributes;
    }
}
