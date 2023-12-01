<?php

namespace App\Rules\Admin;

use App\Models\Employee;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CheckEmployeeExist implements Rule
{
    public function passes($attribute, $value)
    {
        try {
            $user = Auth::user();
            $company_id = $user->company_id;

            $id = request()->id;

            $query = Employee::query()
                ->where('company_id', $company_id)
                ->where(function ($query) use ($value) {
                    $query->where('employee_code', $value)
                        ->orWhere('card_number', $value);
                });
            if ($id) {
                $query->where('id', '<>', $id);
            }

            return !$query->exists();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function message()
    {
        return __('message.data_exits');
    }
}
