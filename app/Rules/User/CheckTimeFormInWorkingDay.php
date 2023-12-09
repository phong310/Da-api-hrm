<?php

namespace App\Rules\User;

use App\Repositories\Interfaces\WorkingDayInterface;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CheckTimeFormInWorkingDay implements Rule
{
    /**
     * @var WorkingDayInterface
     */
    private $workingDay;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(WorkingDayInterface $workingDay)
    {
        $this->workingDay = $workingDay;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $user = Auth::user();
        $company_id = $user->company_id;

        return $this->workingDay->isTimeInWorkingDay($company_id, $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('message.not_in_working_day');
    }
}
