<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use App\Rules\Admin\CheckTimeLunchBreakExist;
use App\Rules\Admin\CheckDateCompenSatoryWorkingDayExist;
use App\Rules\Admin\CheckNameCompenSatoryWorkingDayExist;

class StoreOrUpdateCompensatoryWDRequest extends BaseRequest
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
            'name' => ['required', new CheckNameCompenSatoryWorkingDayExist()],
            'type' => 'required',
            'start_date' => ['required', new CheckDateCompenSatoryWorkingDayExist()],
            'end_date' => ['required', 'after_or_equal:start_date', new CheckDateCompenSatoryWorkingDayExist()],
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'start_lunch_break' => ['nullable', new CheckTimeLunchBreakExist()],
            'end_lunch_break' => ['nullable', 'after:start_lunch_break', new CheckTimeLunchBreakExist()],

        ];
    }

    public function attributes()
    {
        return [
            'start_date' => __('attributes.start_date'),
            'end_date' => __('attributes.end_date'),
            'start_time' => __('attributes.start_time'),
            'end_time' => __('attributes.end_time'),
            'start_lunch_break' => __('attributes.start_lunch_break'),
            'end_lunch_break' => __('attributes.end_lunch_break'),
        ];
    }
}
