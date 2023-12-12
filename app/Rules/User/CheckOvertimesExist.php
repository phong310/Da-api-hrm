<?php

namespace App\Rules\User;

use App\Http\Services\v1\User\SettingUserService;
use App\Models\Form\OverTime;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CheckOvertimesExist implements Rule
{
    /**
     * @var
     */
    protected $value;
    /**
     * @var SettingUserService
     */
    protected $settingService;

    public function __construct()
    {
        $this->settingService = new SettingUserService();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $user = Auth::user();
        $id = request()->id;
        $company_id = $user->company_id;
        $employee_id = $user->employee_id;

        $date = Carbon::parse($value)->format('Y-m-d');
        $this->value = $date;

        $query = OverTime::query()
            ->where(['employee_id' => $employee_id, 'company_id' => $company_id, 'date' => $value])
            ->whereNotIn('status', [OverTime::STATUS['REJECTED'], OverTime::STATUS['CANCEL']])
            ->whereDate('start_time', '=', $date);
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
        $value = $this->settingService->convertFormatDate($this->value);

        return __('message.run_out_of_application', ['value' => $value, 'attribute' => '', 'application_type' => __('form.overtime')]);
    }
}
