<?php

namespace App\Rules\Admin;

use App\Models\User;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CheckUserExist implements Rule
{
    public function passes($attribute, $value)
    {
        try {
            $user = Auth::user();
            $company_id = $user->company_id;

            $id = request()->id;

            $query = User::query()
                ->where('company_id', $company_id)
                ->where(function ($query) use ($value) {
                    $query->where('email', $value)
                        ->orWhere('user_name', $value);
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
