<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use App\Rules\Admin\CheckAllowanceExist;
use App\Rules\Admin\CheckKindOfLeaveExist;

class StoreOrUpdateAllowanceRequest extends BaseRequest
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
            'name' => ['required', new CheckAllowanceExist()],
            'status' => 'required',
            'amount_of_money' => 'required|gt:0',
        ];
    }

    public function attributes()
    {
        $attributes = [
            'name' => __('attributes.name'),
            'status' => __('attributes.status'),
            'amount_of_money' => __('attributes.amount_of_money'),
        ];

        return $attributes;
    }
}
