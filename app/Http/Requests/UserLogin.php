<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\contracts\Validation\Validator;

class UserLogin extends FormRequest
{
    // /**
    //  * Determine if the user is authorized to make this request.
    //  *
    //  * @return bool
    //  */
    // public function authorize()
    // {
    //     return true;
    // }

    // /**
    //  * Get the validation rules that apply to the request.
    //  *
    //  * @return array<string, mixed>
    //  */
    // public function rules()
    // {
    //     return [
    //         "email" => 'required|string|',
    //         "password" => 'required|string'
    //     ];
    // }

    // public function failedValidation(Validator $validator)
    // {
    //     throw new HttpResponseException(response()->json([$validator->errors()], 402));
    // }

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
            //
        ];
    }
}
