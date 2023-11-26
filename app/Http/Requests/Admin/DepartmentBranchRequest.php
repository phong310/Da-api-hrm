<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class DepartmentBranchRequest extends BaseRequest
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
        return [
            'departments.*' => 'required|distinct|max:48',
            'branchs.*' => 'required|distinct|max:48',
        ];
    }

    public function messages()
    {
        return [
            'departments.*.required' => 'validate.required.department',
            'departments.*.distinct' => 'validate.distinct.department',
            'departments.*.max' => 'validate.max_character_48',

            'branchs.*.required' => 'validate.required.branch',
            'branchs.*.distinct' => 'validate.distinct.branch',
            'branchs.*.max' => 'validate.max_character_48',
        ];
    }
}
