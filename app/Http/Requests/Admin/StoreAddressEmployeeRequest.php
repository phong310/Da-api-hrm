<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddressEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];

        $data_resident = request()->RESIDENT;
        if ($data_resident['address'] || $data_resident['province'] || $data_resident['district'] || $data_resident['ward']) {
            $rules['RESIDENT.*'] = ['required', 'max:100'];
        }
        $data_domicile = request()->DOMICILE;
        if ($data_domicile['address'] || $data_domicile['province'] || $data_domicile['district'] || $data_domicile['ward']) {
            $rules['DOMICILE.*'] = ['required', 'max:100'];
        }

        return $rules;
    }
    public function attributes(): array
    {
        return [
            'RESIDENT.province' => __('attributes.province'),
            'RESIDENT.district' => __('attributes.district'),
            'RESIDENT.ward' => __('attributes.ward'),
            'RESIDENT.address' => __('attributes.address'),

            'DOMICILE.province' => __('attributes.province'),
            'DOMICILE.district' => __('attributes.district'),
            'DOMICILE.ward' => __('attributes.ward'),
            'DOMICILE.address' => __('attributes.address'),
        ];
    }
}
