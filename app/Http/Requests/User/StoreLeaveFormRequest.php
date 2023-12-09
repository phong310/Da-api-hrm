<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;
use App\Repositories\Interfaces\Forms\CompensatoryLeaveInterface;
use App\Repositories\Interfaces\Forms\LeaveFormInterface;
use App\Repositories\Interfaces\WorkingDayInterface;
use App\Rules\User\CheckRangeTimeLeaveFormExist;
use App\Rules\User\CheckTimeFormInHoliday;
use App\Rules\User\CheckTimeFormInWorkingDay;

class StoreLeaveFormRequest extends BaseRequest
{
    /**
     * @var WorkingDayInterface
     */
    private $workingDay;
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
    public function __construct(
        WorkingDayInterface $workingDay,
        LeaveFormInterface $leaveForm,
        CompensatoryLeaveInterface $compensatoryLeave
    ) {
        $this->workingDay = $workingDay;
        $this->leaveForm = $leaveForm;
        $this->compensatoryLeave = $compensatoryLeave;
        parent::__construct();
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'start_time' => [
                'required', new CheckRangeTimeLeaveFormExist($this->leaveForm, $this->compensatoryLeave),
                new CheckTimeFormInWorkingDay($this->workingDay), new CheckTimeFormInHoliday()
            ],
            'end_time' => ['required', 'after:start_time', new CheckTimeFormInWorkingDay($this->workingDay), new CheckTimeFormInHoliday()],
            'reason' => 'required|string|nullable',
            'note' => 'string|nullable',
            'kind_leave_id' => 'required',
            'approval_deadline' => 'string',
            'approver_id_1' => 'required|different:approver_id_2',
            'approver_id_2' => 'different:approver_id_1',
        ];
    }

    public function attributes(): array
    {
        return [
            'kind_leave_id' =>  __('message.kind_leave_id'),
            'reason' => __('message.reason'),
            'start_time' =>  __('message.start_time'),
            'end_time' =>  __('message.end_time'),
            'approver_id_1' =>  __('message.approver_id_1'),
            'approver_id_2' =>  __('message.approver_id_2'),
        ];
    }
}
