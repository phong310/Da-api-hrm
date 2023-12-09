<?php

namespace App\Rules\User;

use App\Models\Master\Holiday;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CheckTimeFormInHoliday implements Rule
{
    public function passes($attribute, $value): bool
    {
        try {
            $user = Auth::user();
            $company_id = $user->company_id;

            $startDate = Carbon::parse(request()->start_time)->format('Y-m-d');
            $endDate = Carbon::parse(request()->end_time)->format('Y-m-d');
            $id = request()->id;

            $query = Holiday::where('company_id', $company_id)
                //                ->where('type', Holiday::TYPE['SINGLE_USE'])
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->where(function ($query) use ($startDate, $endDate) {
                        $query->whereBetween('start_date', [$startDate, $endDate])
                            ->orWhereBetween('end_date', [$startDate, $endDate]);
                    })
                        ->orWhere(function ($query) use ($startDate, $endDate) {
                            $query->where('start_date', '<=', $startDate)
                                ->where('end_date', '>=', $endDate);
                        });
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
        return __('message.leave_form_holiday_exist');
    }
}
