<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;

class StoreWorkingDayUserRequest extends BaseRequest
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
            'name' => 'required',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'start_lunch_break' => 'required',
            'end_lunch_break' => 'required|after:start_lunch_break',
            'day_in_week_id' => 'required',

            'format_date' => 'required',
            'time_zone' => 'required',
            'locale' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'day_in_week_id.required' => __('message.validate.required.day_in_week_id'),
            'format_date.required' => __('message.validate.required.format_date'),
            'time_zone.required' => __('message.validate.required.time_zone'),
            'locale.required' => __('message.validate.required.locale'),

            'end_time.after' => __('message.validate.working_day.end_time_after_start_time'),
            'end_lunch_break.after' => __('message.validate.working_day.end_lunch_break_after_start_lunch_break')
        ];
    }
}
