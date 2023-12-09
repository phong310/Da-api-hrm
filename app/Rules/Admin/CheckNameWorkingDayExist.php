<?php

namespace App\Rules\Admin;

use App\Models\Master\WorkingDay;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CheckNameWorkingDayExist implements Rule
{
    public function passes($attribute, $value)
    {
        $user = Auth::user();
        $company_id = $user->company_id;
        $id = request()->id;
        $type = request()->type;
        $query = WorkingDay::query()
            ->where(['company_id' => $company_id, 'type' => $type, 'name' => $value]);
        if ($id) {
            $query->where('id', '!=', $id);
        }
        if ($query->exists()) {
            return false;
        }

        return true;
    }
    public function message()
    {
        return __('message.data_exits');
    }
}
