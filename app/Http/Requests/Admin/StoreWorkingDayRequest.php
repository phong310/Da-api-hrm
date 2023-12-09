<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use App\Rules\Admin\CheckTimeLunchBreakExist;
use App\Rules\Admin\CheckWorkingDayExist;
use App\Rules\Admin\CheckNameWorkingDayExist;

class StoreWorkingDayRequest extends BaseRequest
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
            'name' => ['required',  new CheckNameWorkingDayExist()],
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'start_lunch_break' => ['nullable', new CheckTimeLunchBreakExist()],
            'end_lunch_break' => ['nullable', 'after:start_lunch_break', new CheckTimeLunchBreakExist()],
            'day_in_week_id' => ['required', new CheckWorkingDayExist()],
        ];
    }

    public function attributes()
    {
        $attributes = [
            'name' => __('attributes.name'),
            'start_time' => __('attributes.start_time'),
            'end_time' => __('attributes.end_time'),
            'start_lunch_break' => __('attributes.start_lunch_break'),
            'end_lunch_break' => __('attributes.end_lunch_break'),
            'day_in_week_id' => __('attributes.day_in_week_id'),
        ];

        return $attributes;
    }
}
