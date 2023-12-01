<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUpdateRelativeEmployee extends FormRequest
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

        $rules['first_name'] = ['required', 'max:50'];
        $rules['last_name'] = ['required', 'max:50'];
        $rules['birthday'] = ['required'];
        $rules['relationship_type'] = ['required'];
        $rules['ward'] = ['required', 'max:50'];
        $rules['address'] = ['required', 'max:50'];
        $rules['district'] = ['required', 'max:50'];
        $rules['province'] = ['required', 'max:50'];
        $rules['phone'] = ['required', 'regex:/^[0-9]{9,12}$/'];
        $rules['sex'] = ['required'];
        $rules['employee_id'] = ['required'];

        return $rules;
    }

    public function attributes()
    {
        return [
            'first_name' => __('attributes.first_name'),
            'last_name' => __('attributes.last_name'),
            'relationship_type' => __('attributes.relationship_type'),
            'ward' => __('attributes.ward'),
            'address' => __('attributes.address'),
            'district' => __('attributes.district'),
            'province' => __('attributes.province'),
            'phone' => __('attributes.phone'),
            'sex' => __('attributes.sex'),
        ];
    }
}
