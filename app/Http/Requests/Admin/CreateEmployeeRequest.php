<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use App\Rules\Admin\CheckEmployeeExist;
use App\Rules\Admin\CheckInformationExist;
use App\Rules\Admin\CheckUserExist;

class CreateEmployeeRequest extends BaseRequest
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
        $rules = [
            //Users

            'user_email' => ['required', 'email', new CheckUserExist()],
            'user_name' => ['required', new CheckUserExist()],
            'password' => 'required',
            'role_id' => 'required',
            //Information
            'first_name' => 'required',
            'last_name' => 'required',
            'sex' => 'required',
            'email' => ['required', 'email', new CheckInformationExist()],
            // 'job_id' => 'required',
            'phone' => 'required',
            'country_id' => 'required',
            //Employee
            'card_number' => ['nullable', new CheckEmployeeExist()],
            'employee_code' => ['required', new CheckEmployeeExist()],
            //Address
            //            'province' => 'required',
            //            'district' => 'required',
            //            'ward' => 'required',
            //            'addressType' => 'required',
            //            'address' => 'required',
            //account_BANK
            'account_number' => 'nullable',
        ];
        if (
            request()->province ||
            request()->district ||
            request()->ward ||
            request()->address
        ) {
            $rules['province'] = 'required';
            $rules['district'] = 'required';
            $rules['ward'] = 'required';
            $rules['address'] = 'required';
        }

        //        if (
        //            request()->account_number ||
        //            request()->account_name ||
        //            request()->bank_branch ||
        //            request()->bank_type ||
        //            request()->bank_branch
        //        ) {
        //            $rules['account_number'] = 'required';
        //            $rules['account_name'] = 'required';
        //            $rules['bank_type'] = 'required';
        //            $rules['bank_branch'] = 'required';
        //            $rules['bank_name'] = 'required';
        //        }
        //        if (
        //            request()->school_name ||
        //            request()->from_date ||
        //            request()->to_date ||
        //            request()->description
        //        ) {
        //            $rules['description'] = 'required';
        //            $rules['to_date'] = 'required';
        //            $rules['from_date'] = 'required';
        //            $rules['school_name'] = 'required';
        //        }
        if (
            request()->ID_no ||
            request()->issued_date ||
            request()->issued_by ||
            request()->ID_expire
        ) {
            $rules['ID_no'] = 'required';
            $rules['issued_date'] = 'required';
            $rules['issued_by'] = 'required';
            $rules['ID_expire'] = 'required';
        }

        return  $rules;
    }


    public function attributes()
    {
        return [
            'user_email' => __('attributes.user_email'),
            'user_name' => __('attributes.user_name'),
            'card_number' => __('attributes.card_number'),
            'province' => __('attributes.province'),
            'employee_code' => __('attributes.employee_code'),
            'district' => __('attributes.district'),
            'ward' => __('attributes.ward'),
            'issued_by' => __('attributes.issued_by'),
            'issued_date' => __('attributes.issued_date'),
            'ID_expire' => __('attributes.ID_expire'),

            'ID_no' => __('attributes.ID_no'),
            //            'ID_expire' => __('attributes.employee_code'),
            //            'ID_expire' => __('attributes.employee_code'),
            //            'ID_expire' => __('attributes.employee_code'),
            //            'ID_expire' => __('attributes.employee_code'),

        ];
    }
}
