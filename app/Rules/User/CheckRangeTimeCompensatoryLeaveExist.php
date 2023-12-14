<?php

namespace App\Rules\User;

use App\Http\Services\v1\User\SettingUserService;
use App\Repositories\Interfaces\Forms\CompensatoryLeaveInterface;
use App\Repositories\Interfaces\Forms\LeaveFormInterface;
use App\Traits\CalculateTime;
use App\Traits\SystemSetting;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class CheckRangeTimeCompensatoryLeaveExist implements Rule
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
    public function __construct(LeaveFormInterface $leaveForm, CompensatoryLeaveInterface $compensatoryLeave)
    {
        $this->settingService = new SettingUserService();
        $this->leaveForm = $leaveForm;
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
        $startTime = $this->replaceSlashesToDashes(request()->start_time);
        $endTime = $this->replaceSlashesToDashes(request()->end_time);

        $this->value = $value;
        $date = Carbon::parse($value)->format('Y-m-d');

        // TODO: Check start_time và end_time đã tồn tại trong đơn khác
        if (
            $this->leaveForm->checkFormIsExistByStartEndTime($startTime, $endTime, null) ||
            $this->compensatoryLeave->checkFormIsExistByStartEndTime($startTime, $endTime, $id)
        ) {
            return false;
        }

        // TODO: Check thời gian start_time và end_time có bị trùng nhau trong các đơn khác
        $LeaveForms = $this->leaveForm->queryFormByDateExceptId($date, null)->get();

        $forms = $LeaveForms->toArray();

        return $this->validateTime($startTime, $endTime, $forms);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $value = $this->settingService->convertFormatDate($this->value);

        return __('message.exists_time_application', ['value' => $value]);
    }
}
