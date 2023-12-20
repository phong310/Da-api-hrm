<?php

namespace App\Rules\Admin;


use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\OtherAllowance;

class CheckOtherAllowanceExist implements Rule
{
    protected $currentId;

    public function __construct($currentId = null)
    {
        $this->currentId = $currentId;
    }

    public function passes($attribute, $value)
    {
        $user = Auth::user();
        $company_id = $user->company_id;
        $id = request()->id;


        $query = OtherAllowance::query()
            ->where([
                'company_id' => $company_id,
                'name' => $value,
            ]);

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
        return __('validation.unique');
    }
}
