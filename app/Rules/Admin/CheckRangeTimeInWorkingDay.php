<?php

namespace App\Rules\Admin;

use App\Models\Master\WorkingDay;
use App\Models\Setting\SettingTypeOvertime;
use App\Traits\CalculateTime;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CheckRangeTimeInWorkingDay implements Rule
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
        $user = Auth::user();
        $type = request()->type;
        $company_id = $user->company_id;
        $index = explode('.', $attribute)[1];
        $settingOvertime = request()->setting_ot_salary_coefficients;

        $workingDays = WorkingDay::query()->where('company_id', $company_id)->get();
        $settingStartTime = Carbon::parse($settingOvertime[$index]['start_time'])->format('H:i:s');
        $settingEndTime = Carbon::parse($settingOvertime[$index]['end_time'])->format('H:i:s');

        // Loop throught workingDays array
        foreach ($workingDays as $workingDay) {
            $workingStartTime = $workingDay->start_time;
            $workingEndTime = $workingDay->end_time;

            if ($type === SettingTypeOvertime::TYPE['AFTER_OFFICE_HOUR']) {
                if (!($settingEndTime <= $workingStartTime || $settingStartTime >= $workingEndTime)) {
                    return false;
                }
            }
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
        return __('message.not_overtime_in_working_day');
    }
}
