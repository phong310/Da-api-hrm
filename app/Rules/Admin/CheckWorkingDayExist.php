<?php

namespace App\Rules\Admin;

use App\Models\Master\WorkingDay;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CheckWorkingDayExist implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $user = Auth::user();
        $company_id = $user->company_id;
        $id = request()->id;
        $query = WorkingDay::query()->where(['company_id' => $company_id, 'day_in_week_id' => $value]);
        if ($id) {
            $query->where('id', '!=', $id);
        }
        if ($query->exists()) {
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
        return __('message.working_day_exits');
    }
}
