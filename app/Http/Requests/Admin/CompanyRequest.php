<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use App\Rules\PhoneNumber;
use Illuminate\Validation\Rule;

class CompanyRequest extends BaseRequest
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
            'name' => 'required|max:255|unique:companies,name,' . request()->id,
            'phone_number' => [
                'required', Rule::unique('companies', 'phone_number')->ignore(request()->id, 'id'),
                new PhoneNumber()
            ],
            'tax_code' => 'required|max:255|unique:companies,tax_code,' . request()->id,
            'address' => 'required|max:255',
            'representative' => 'required|max:100',
            'type_of_business' => 'required',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date',
            'register_date' => 'nullable|date',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Trường này không được bỏ trống',
            'name.unique' => 'Tên công ty đã tồn tại',
            'phone_number.required' => 'Trường này không được bỏ trống',
            'phone_number.unique' => 'Số điện thoại đã tồn tại',
            'tax_code.required' => 'Trường này không được bỏ trống',
            'tax_code.unique' => 'Trường này không được bỏ trống',
            'address.required' => 'Trường này không được bỏ trống',
            'representative.required' => 'Trường này không được bỏ trống',
            'type_of_business.required' => 'Trường này không được bỏ trống',
        ];
    }
}
