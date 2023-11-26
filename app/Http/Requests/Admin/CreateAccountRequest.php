<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class CreateAccountRequest extends BaseRequest
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
            'password' => 'required|min:6',
            'role' => 'required',
            'user_name' => 'required|max:255|unique:users,user_name,' . request()->id,
            'email' => 'required|max:255|regex:/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/i|unique:users,email,' . request()->id,
        ];
    }

    public function messages()
    {
        return [
            'password.required' => 'validate.required.password',
            'password.min' => 'validate.min.password',

            'role.required' => 'validate.required.role',

            'user_name.required' => 'validate.required.user_name',
            'user_name.unique' => 'validate.unique.user_name',

            'email.required' => 'validate.required.email',
            'email.unique' => 'validate.unique.email',
            'email.regex' => 'validate.invalid.email',
        ];
    }
}
