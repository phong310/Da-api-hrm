<?php

namespace App\Http\Requests\Admin;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrUpdateEducationEmployeeRequest extends FormRequest
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
        $rules = [];

        $rules['educations.*.school_name'] = ['required', 'max:50'];
        $rules['educations.*.description'] = ['required'];
        $rules['educations.*.from_date'] = ['required'];
        $rules['educations.*.to_date'] = ['required', 'after:educations.*.from_date'];



        return $rules;
    }

    public function messages(): array
    {
        return [
            'educations.*.school_name.required' => __("message.school_name_empty"),
            'educations.*.description.required' => __("message.validate_descriptions"),
            'educations.*.to_date.after' => __('message.validate_to_date'),
            'educations.*.school_name.max' => __('message.validate_school_name'),
        ];
    }
}
