<?php

namespace App\Rules\Admin;

use App\Models\PersonalInformation;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CheckInformationExist implements Rule
{
    public function passes($attribute, $value)
    {
        try {
            $user = Auth::user();
            $company_id = $user->company_id;
            $id = request()->information['id'];
            $email = request()->information['email'];

            $query = PersonalInformation::query()
                ->whereHas('employee', function ($query) use ($company_id) {
                    $query->where('company_id', $company_id);
                })
                ->where('email', $email);

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
