<?php

namespace App\Http\Requests\User;

use App\Rules\User\CheckDateRequestChangeTimeSheet;
use App\Rules\User\CheckRequestChangeTimesheetExist;
use App\Rules\User\CheckTimeFormInWorkingDay;
use Illuminate\Foundation\Http\FormRequest;
use App\Repositories\Interfaces\WorkingDayInterface;

class StoreOrUpdateRequestChangeTimesheetRequest extends FormRequest
{

    private $workingDay;
    public function __construct(WorkingDayInterface $workingDay)
    {
        $this->workingDay = $workingDay;
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
            'check_in_time' => ['required', new CheckTimeFormInWorkingDay($this->workingDay)],
            'check_out_time' => ['required', 'after:check_in_time', new CheckTimeFormInWorkingDay($this->workingDay)],
            'note' => 'required|string|nullable',
            'date' => ['required', new CheckRequestChangeTimesheetExist(), new CheckDateRequestChangeTimeSheet($this->workingDay)],
            'approver_id_1' => 'required|different:approver_id_2',
            'approver_id_2' => 'different:approver_id_1',
        ];
    }

    public function messages()
    {
        return [
            'check_in_time.required' => 'validate.required.start_time',
            'check_out_time.required' => 'validate.required.end_time',
            'check_out_time.after' => 'validate.after_start_time',
            'approver_id.required' => 'validate.required.approver_id',
        ];
    }

    public function attributes()
    {
        return [
            'approver_id_1' =>  __('message.approver_id_1'),
            'approver_id_2' =>  __('message.approver_id_2'),
            'note' =>  __('message.note'),
            'check_in_time' =>  __('message.start_time'),
            'check_out_time' =>  __('message.end_time'),
        ];
    }
}
