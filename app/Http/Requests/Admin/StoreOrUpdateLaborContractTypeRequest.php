<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use App\Rules\Admin\CheckAllowanceExist;
use App\Rules\Admin\CheckLaborContractTypeExist;

class StoreOrUpdateLaborContractTypeRequest extends BaseRequest
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
        return [
            'name' => ['required', new CheckLaborContractTypeExist()],
            'duration_of_contract' => 'nullable',
            'allowances' => 'required',
        ];
    }

    public function attributes()
    {
        $attributes = [
            'name' => __('attributes.name'),
            'duration_of_contract' => __('attributes.duration_of_contract'),
            'allowances' => __('attributes.allowances'),
        ];

        return $attributes;
    }
}
