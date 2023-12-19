<?php

namespace App\Http\Requests\Admin;

use App\Rules\Admin\CheckRangeTimeInWorkingDay;
use App\Rules\Admin\CheckRangeTimeSettingTypeOTExist;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrUpdateSettingTypeOvertime extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            'setting_ot_salary_coefficients.*.start_time' => ['required', new CheckRangeTimeSettingTypeOTExist(), new CheckRangeTimeInWorkingDay()],
            'setting_ot_salary_coefficients.*.end_time' => ['required', 'after:setting_ot_salary_coefficients.*.start_time'],
            'setting_ot_salary_coefficients.*.salary_coefficient' => ['required'],
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'setting_ot_salary_coefficients.*.start_time.required' => __('validate.required.start_time'),
            'setting_ot_salary_coefficients.*.end_time.required' => __('validate.required.end_time'),
            'setting_ot_salary_coefficients.*.end_time.after' => __('message.end_time_after_start_time'),
            'setting_ot_salary_coefficients.*.salary_coefficient.required' => __('validate.required.salary_coefficient'),
        ];
    }
}
