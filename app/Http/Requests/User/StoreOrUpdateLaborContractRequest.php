<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseRequest;
use App\Models\LaborContract\LaborContract;
use App\Repositories\Interfaces\LaborContract\LaborContractInterface;
use App\Rules\User\CheckLaborContractActiveExist;
use App\Rules\User\CheckLaborContractCodeExit;
use App\Rules\ExpireDateAfterSignDate;
use Carbon\Carbon;

class StoreOrUpdateLaborContractRequest extends BaseRequest
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
        $contract = app(LaborContractInterface::class);

        $rules = [
            'employee_id' => ['required', new CheckLaborContractActiveExist($contract)],
            'code' => ['required', new CheckLaborContractActiveExist($contract)],
            'labor_contract_type_id' => 'required',
            'allowances' => 'required',
            'sign_date' => 'required',
            'effective_date' => 'required',
            'status' => 'required',
            'basic_salary' => 'required',
            'insurance_salary' => 'required',
            'hourly_salary' => 'required',
            'expire_date' => 'after:effective_date'
        ];


        if (request()->expire_date) {
            $expireDate = Carbon::parse(request()->expire_date);
            $signDate = Carbon::parse(request()->sign_date);
            $rules['expire_date'] = [
                'required',
                'after:sign_date',
                'after:effective_date',
                function ($attribute, $value, $fail) use ($expireDate, $signDate) {

                    $now = Carbon::now();
                    if ($expireDate < $signDate) {
                        $fail(__('message.labor_sign_date'));
                    } else if ($expireDate < $now) {
                        $fail(__('message.labor_current_date'));
                    }
                },
            ];
        }

        if (request()->status == LaborContract::STATUS['TERMINATE']) {
            $rules['termination_date'] = 'required';
            $rules['reason_contract_termination'] = 'required';
            $rules['expire_date'] = '';
        }

        return $rules;
    }

    public function attributes()
    {
        $attributes = [
            'employee_id' => __('attributes.employee'),
            'code' => __('attributes.code_contract'),
            'labor_contract_type_id' => __('attributes.labor_contract_type'),
            'sign_date' => __('attributes.sign_date'),
            'expire_date' => __('attributes.expire_date'),
            'effective_date' => __('attributes.effective_date'),
            'allowances' => __('attributes.allowances'),
            'status' => __('attributes.status'),
            'basic_salary' => __('attributes.basic_salary'),
            'insurance_salary' => __('attributes.insurance_salary'),
            'hourly_salary' => __('attributes.hourly_salary'),
            'termination_date' => __('attributes.termination_date'),
            'reason_contract_termination' => __('attributes.reason_contract_termination'),
        ];

        return $attributes;
    }
}
