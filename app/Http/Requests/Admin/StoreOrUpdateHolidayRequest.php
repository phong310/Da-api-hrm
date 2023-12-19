<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use App\Rules\Admin\CheckDateHolidayExist;
use App\Rules\Admin\CheckNameHolidayExist;

class StoreOrUpdateHolidayRequest extends BaseRequest
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
            'name' => ['required', new CheckNameHolidayExist()],
            'type' => 'required',
            'start_date' => [
                'required', new CheckDateHolidayExist()
            ],
            //            'start_date' => ['required', new CheckHolidayExist()],
            'end_date' =>  [
                'required', 'after_or_equal:start_date', new CheckDateHolidayExist()
            ],
        ];
    }
    public function attributes()
    {
        $attributes = [
            'name' => __('attributes.name'),
            'start_time' => __('attributes.start_time'),
            'end_time' => __('attributes.end_time'),
            'start_date' => __('attributes.start_date'),
            'end_date' => __('attributes.end_date'),
            'day_in_week_id' => __('attributes.day_in_week_id'),
        ];

        return $attributes;
    }
}
