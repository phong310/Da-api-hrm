<?php

namespace App\Rules\Admin;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CheckNameMaster implements Rule
{
    protected $model;
    protected $companyId;

    public function __construct($model)
    {
        $user = Auth::user();
        $this->companyId = $user->company_id;
        $this->model = $model;
    }

    public function passes($attribute, $value)
    {
        $id = request()->id;

        $query = $this->model::query()
            ->where('company_id', $this->companyId)
            ->where('name', $value);

        if ($id) {
            $query->where('id', '!=', $id);
        }

        return !$query->exists();
    }

    public function message()
    {
        return __('message.data_exits');
    }
}
