<?php

namespace App\Rules\Admin;

use App\Models\LaborContract\Allowance;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CheckAllowanceExist implements Rule
{
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
        $company_id = $user->company_id;
        $id = request()->id;

        $query = Allowance::query()
            ->where([
                'company_id' => $company_id,
                'name' => $value,
                'status' => Allowance::STATUS['ACTIVE']
            ]);

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
        return __('message.data_exits');
    }
}
