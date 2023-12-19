<?php

namespace App\Rules\Admin;

use App\Traits\CalculateTime;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class CheckRangeTimeSettingTypeOTExist implements Rule
{
    use CalculateTime;

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $times = request()->setting_ot_salary_coefficients;

        //TODO: setting_ot_salary_coefficients.index.field_name
        $index = explode('.', $attribute)[1];
        $startTime = Carbon::parse($times[$index]['start_time'])->format('H:i:s');
        $endTime = Carbon::parse($times[$index]['end_time'])->format('H:i:s');
        unset($times[$index]);

        if (!$this->validateTime($startTime, $endTime, $times)) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('message.overtime_overlaps');
    }
}
