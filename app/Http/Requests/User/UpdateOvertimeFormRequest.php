<?php

namespace App\Http\Requests\User;

use App\Http\Services\v1\Admin\CompanyService;
use App\Repositories\Interfaces\SettingTypesOvertimeInterface;
use App\Rules\User\CheckTimeOverTimeExist;
use App\Rules\User\CheckOvertimesExist;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOvertimeFormRequest extends FormRequest
{
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
        $settingTypesOverTime = app(SettingTypesOvertimeInterface::class);
        $companyService = app(CompanyService::class);
        return [
            'date' => ['required', new CheckOvertimesExist()],
            'start_time' => ['required', new CheckTimeOverTimeExist($settingTypesOverTime, $companyService)],
            'end_time' => 'required|after:start_time',
            'reason' => 'required',
            'note' => 'string|nullable',
            'approval_deadline' => 'string',
            'approver_id_1' => 'required|different:approver_id_2',
            'approver_id_2' => 'different:approver_id_1',
        ];
    }

    public function attributes()
    {
        return [
            'reason' =>  __('message.reason'),
            'start_time' =>  __('message.start_time'),
            'end_time' =>  __('message.end_time'),
            'approver_id_1' =>  __('message.approver_id'),
            'approver_id_2' =>  __('message.approver_id'),
        ];
    }
}
