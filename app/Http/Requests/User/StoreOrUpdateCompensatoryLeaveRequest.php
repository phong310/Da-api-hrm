<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;
use App\Repositories\Interfaces\Forms\CompensatoryLeaveInterface;
use App\Repositories\Interfaces\Forms\LeaveFormInterface;
use App\Repositories\Interfaces\WorkingDayInterface;
use App\Rules\User\CheckCompensatoryLeaveExist;
use App\Rules\User\CheckRangeTimeCompensatoryLeaveExist;
use App\Rules\User\CheckTimeFormInWorkingDay;

class StoreOrUpdateCompensatoryLeaveRequest extends BaseRequest
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
        WorkingDayInterface        $workingDay,
        LeaveFormInterface         $leaveForm,
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
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'start_time' => [
                'required',
                new CheckCompensatoryLeaveExist($this->compensatoryLeave),
                new CheckTimeFormInWorkingDay($this->workingDay),
                new CheckRangeTimeCompensatoryLeaveExist($this->leaveForm, $this->compensatoryLeave)
            ],
            'end_time' => ['required', 'after:start_time', new CheckTimeFormInWorkingDay($this->workingDay)],
            'reason' => 'required|string|nullable',
            'note' => 'string|nullable',
            // 'kind_leave_id' => 'required',
            'approval_deadline' => 'string',
            'approver_id_1' => 'required|different:approver_id_2',
            'approver_id_2' => 'different:approver_id_1',
        ];
    }

    public function attributes()
    {
        return [
            'kind_leave_id' => __('message.kind_leave_id'),
            'start_time' => __('message.start_time'),
            'end_time' => __('message.end_time'),
            'approver_id' => __('message.approver_id'),
            'approver_id_1' => __('message.approver_id_1'),
            'approver_id_2' => __('message.approver_id_2'),
            'reason' => __('message.reason'),
        ];
    }
}
