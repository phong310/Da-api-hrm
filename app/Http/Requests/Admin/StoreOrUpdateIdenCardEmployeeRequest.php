<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrUpdateIdenCardEmployeeRequest extends FormRequest
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
        $rules = [];

        $data_cmt = request()->CMT;
        if ($data_cmt['ID_expire'] || $data_cmt['ID_no'] || $data_cmt['issued_by'] || $data_cmt['issued_date']) {
            $rules['CMT.ID_expire'] = ['required'];
            $rules['CMT.ID_no'] = ['required', 'max:13', 'gt:0'];
            $rules['CMT.issued_by'] = ['required', 'max:255'];
            $rules['CMT.issued_date'] = ['required', 'before:CMT.ID_expire'];
        }

        $data_tcc = request()->TCC;
        if ($data_tcc['ID_expire'] || $data_tcc['ID_no'] || $data_tcc['issued_by'] || $data_tcc['issued_date']) {
            $rules['TCC.ID_expire'] = ['required'];
            $rules['TCC.ID_no'] = ['required', 'max:13'];
            $rules['TCC.issued_by'] = ['required', 'max:255'];
            $rules['TCC.issued_date'] = ['required', 'before:CMT.ID_expire'];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'CMT.issued_by' => __('attributes.issued_by'),
            'CMT.ID_no' => __('attributes.ID_no'),
            'CMT.issued_date' => __('attributes.issued_date'),
            'CMT.ID_expire' => __('attributes.ID_expire'),

            'TCC.issued_by' => __('attributes.issued_by'),
            'TCC.ID_no' => __('attributes.ID_no'),
            'TCC.issued_date' => __('attributes.issued_date'),
            'TCC.ID_expire' => __('attributes.ID_expire')
        ];
    }
}
