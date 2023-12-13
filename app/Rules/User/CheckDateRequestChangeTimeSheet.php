<?php

namespace App\Rules\User;

use App\Http\Services\v1\User\SettingUserService;
use App\Repositories\Interfaces\WorkingDayInterface;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;


class CheckDateRequestChangeTimeSheet implements Rule
{
    private $workingDay;
    protected $value;
    protected $settingService;

    public function __construct(WorkingDayInterface $workingDay)
    {
        $this->workingDay = $workingDay;
        $this->settingService = new SettingUserService();
    }
    public function passes($attribute, $value)
    {
        $user = Auth::user();
        $company_id = $user->company_id;
        $date = request()->date;
        $this->value = $date;
        $workingDate =
            $this->workingDay->showWorkingDayByDate($company_id, $date);
        if (!$workingDate) {
            return false;
        }
        return true;
    }

    public function message()
    {
        $value = $this->settingService->convertFormatDate($this->value);

        return __('message.not_in_working_day_date', ['value' => $value]);
    }
}
