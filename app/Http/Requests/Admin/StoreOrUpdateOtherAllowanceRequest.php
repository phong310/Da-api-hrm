<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use App\Rules\Admin\CheckOtherAllowanceExist;

class StoreOrUpdateOtherAllowanceRequest extends BaseRequest
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
            'name' => ['required', new CheckOtherAllowanceExist()],
        ];
    }

    public function attributes()
    {
        $attributes = [
            'name' => __('attributes.name'),
        ];

        return $attributes;
    }
}
