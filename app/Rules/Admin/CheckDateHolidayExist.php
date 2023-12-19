<?php

namespace App\Rules\Admin;

use Illuminate\Contracts\Validation\Rule;
use App\Traits\SystemSetting;
use Illuminate\Support\Facades\Auth;
use App\Models\Master\Holiday;

class CheckDateHolidayExist implements Rule
{
    use SystemSetting;

    public function passes($attribute, $value)
    {
        try {
            $user = Auth::user();
            $company_id = $user->company_id;
            $startDate = $this->replaceSlashesToDashes(request()->start_date);
            $endDate = $this->replaceSlashesToDashes(request()->end_date);
            $id = request()->id;
            $type = request()->type;

            if (!empty($startDate) || !empty($endDate)) {
                $query = Holiday::query()->where('company_id', $company_id)->where('type',  $type)
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
            }


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
        return __('message.salary_sheets_date_exists');
    }
}
