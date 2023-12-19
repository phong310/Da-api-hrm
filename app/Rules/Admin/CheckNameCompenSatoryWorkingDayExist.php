<?php

namespace App\Rules\Admin;

use App\Models\Master\CompensatoryWorkingDay;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CheckNameCompenSatoryWorkingDayExist implements Rule
{
    public function passes($attribute, $value)
    {
        $user = Auth::user();
        $company_id = $user->company_id;
        $id = request()->id;
        $type = request()->type;
        $query = CompensatoryWorkingDay::query()
            ->where(['company_id' => $company_id, 'name' => $value, 'type' => $type]);
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
