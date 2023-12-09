<?php

namespace App\Rules\Admin;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

class CheckTimeLunchBreakExist implements Rule
{
    public function passes($attribute, $value)
    {
        $startTime = $this->parseTime(request('start_time'));
        $endTime = $this->parseTime(request('end_time'));

        if ($startTime && $endTime) {
            return $value >= $startTime && $value <= $endTime;
        }

        return false;
    }

    public function message()
    {
        return __('message.time_lunch_break_exist');
    }
    private function parseTime($time)
    {
        return Carbon::createFromFormat('H:i', $time)->format('H:i');
    }
}
