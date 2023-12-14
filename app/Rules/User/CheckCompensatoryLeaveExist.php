<?php

namespace App\Rules\User;

use App\Http\Services\v1\User\SettingUserService;
use App\Repositories\Interfaces\Forms\CompensatoryLeaveInterface;
use App\Repositories\Interfaces\Forms\LeaveFormInterface;
use App\Traits\CalculateTime;
use App\Traits\SystemSetting;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class CheckCompensatoryLeaveExist implements Rule
{
    use SystemSetting;
    use CalculateTime;

    /**
     * @var SettingUserService
     */
    private $settingService;
    /**
     * @var LeaveFormInterface
     */
    private $leaveForm;
    /**
     * @var CompensatoryLeaveInterface
     */
    private $compensatoryLeave;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(CompensatoryLeaveInterface $compensatoryLeave)
    {
        $this->settingService = new SettingUserService();
        $this->compensatoryLeave = $compensatoryLeave;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $id = request()->id;
        $this->value = $value;
        $date = Carbon::parse($value)->format('Y-m-d');

        $compensatoryLeave = $this->compensatoryLeave->queryFormByDateExceptId($date, $id);

        if ($compensatoryLeave->exists()) {
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

        return __('message.run_out_of_application', ['value' => $value, 'application_type' => __('form.compensatory_leave')]);
    }
}
