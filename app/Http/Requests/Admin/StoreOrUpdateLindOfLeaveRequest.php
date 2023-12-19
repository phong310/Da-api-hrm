<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use App\Rules\Admin\CheckKindOfLeaveExist;

class StoreOrUpdateLindOfLeaveRequest extends BaseRequest
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
            'name' => ['required', new CheckKindOfLeaveExist('name')],
            'symbol' => ['required', new CheckKindOfLeaveExist('symbol')],
        ];
    }
    public function attributes()
    {
        $attributes = [
            'name' => __('attributes.name_kind_of_leave'),
            'symbol' => __('attributes.symbol'),
        ];

        return $attributes;
    }
}
