<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use App\Rules\Admin\CheckEmployeeExist;
use App\Rules\Admin\CheckInformationExist;


class UpdateEmployeeRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        $rules = [
            'information.email' => ['required', 'email', new CheckInformationExist()],
            'card_number' => ['nullable', new CheckEmployeeExist()],
            'employee_code' => ['required', new CheckEmployeeExist()],
        ];

        return  $rules;
    }

    public function attributes()
    {
        return [
            'information.email' => __('attributes.user_email'),
            'card_number' => __('attributes.card_number'),
            'employee_code' => __('attributes.employee_code'),
        ];
    }
}
