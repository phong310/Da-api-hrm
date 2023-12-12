<?php

namespace App\Rules\User;

use App\Http\Services\v1\Admin\CompanyService;
use App\Repositories\Interfaces\SettingTypesOvertimeInterface;
use App\Traits\SystemSetting;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CheckTimeOverTimeExist implements Rule
{
    use SystemSetting;

    protected $settingTypesOvertime;
    protected $companyService;

    public function __construct(SettingTypesOvertimeInterface $settingTypesOvertime, CompanyService $companyService)
    {
        $this->settingTypesOvertime = $settingTypesOvertime;
        $this->companyService = $companyService;
    }

    public function passes($attribute, $value)
    {
        $user = Auth::user();
        $companyId = $user->company_id;
        $setting = $this->companyService->getSettingOfCompany($companyId);
        $timezone = $setting->time_zone;
        $startTime = request()->start_time;
        $endTime = request()->end_time;
        $date = request()->date;

        $startTimeTz = $this->convertDateTimeToTZ($startTime, 'UTC', $timezone)->format('H:i:s');
        $endTimeTz = $this->convertDateTimeToTZ($endTime, 'UTC', $timezone)->format('H:i:s');

        $settingTypeOvertime = $this->settingTypesOvertime->showByDate($companyId, $date);
        $settingOTSalaryCoefficient = $settingTypeOvertime->settingOvertimeSalaryCoefficient;

        foreach ($settingOTSalaryCoefficient as $st) {
            if ($startTimeTz >= $st['start_time'] && $endTimeTz <= $st['end_time']) {
                return true;
            }
        }

        return false;
    }

    public function message()
    {
        return __('message.overtime_exist');
    }
}
