<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use App\Rules\PhoneNumber;
use Illuminate\Validation\Rule;

class CompanyNewRequest extends BaseRequest
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
            //'logo' => 'required',
            'type_of_business' => 'required',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date',
            'register_date' => 'nullable|date',
            'departments.*' => 'required|distinct|max:48',
            'branchs.*' => 'required|distinct|max:48',
            'password' => 'required|min:6',
            'role' => 'required',
            'user_name' => 'required|max:255|unique:users,user_name,' . request()->id,
            'email' => 'required|max:255|regex:/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/i|unique:users,email,'
        ];
    }
    public function messages()
    {
        return [
            'name.required' => __('message.validate.required.company.name'),
            'name.unique' => __('message.validate.unique.company.name'),
            'phone_number.required' => __('message.validate.required.company.phone_number'),
            'phone_number.unique' => __('message.validate.unique.company.phone_number'),
            'tax_code.required' => __('message.validate.required.company.tax_code'),
            'tax_code.unique' => __('message.validate.unique.company.tax_code'),
            'address.required' => __('message.validate.required.company.address'),
            'representative.required' => __('message.validate.required.company.representative'),
            'type_of_business.required' => __('message.validate.required.company.type_of_business'),
            'departments.*.required' => 'validate.required.department',
            'departments.*.distinct' => 'validate.distinct.department',
            'departments.*.max' => 'validate.max_character_department_48',

            'day_in_week_id.required' => __('message.validate.required.day_in_week_id'),
            'format_date.required' => __('message.validate.required.format_date'),
            'time_zone.required' => __('message.validate.required.time_zone'),
            'locale.required' => __('message.validate.required.locale'),

            'end_time.after' => __('message.validate.working_day.end_time_after_start_time'),
            'end_lunch_break.after' => __('message.validate.working_day.end_lunch_break_after_start_lunch_break'),

            'branchs.*.required' => 'validate.required.branch',
            'branchs.*.distinct' => 'validate.distinct.branch',
            'branchs.*.max' => 'validate.max_character_branch_48',

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
