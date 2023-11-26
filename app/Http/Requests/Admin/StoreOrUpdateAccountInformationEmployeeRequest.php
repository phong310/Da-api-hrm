<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrUpdateAccountInformationEmployeeRequest extends FormRequest
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
        //
        return [
            'email' => 'required|email|max:100|unique:users,email,' . request()->id,
            'user_name' => 'required|max:50|unique:users,user_name,' . request()->id,
            'role' => 'required',
        ];
    }

    public function attributes()
    {
        $attributes = [
            'email' => __('attributes.user_email'),
            'user_name' => __('attributes.user_name'),
            'role' => __('attributes.role'),
        ];

        return $attributes;
    }
}
