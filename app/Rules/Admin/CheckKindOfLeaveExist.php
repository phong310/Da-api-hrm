<?php

namespace App\Rules\Admin;

use App\Models\Master\KindOfLeave;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class CheckKindOfLeaveExist implements Rule
{
    public $type;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($type)
    {
        $this->type = $type;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $user = Auth::user();
        $company_id = $user->company_id;
        $id = request()->id;
        $query = KindOfLeave::query()
            ->where(['company_id' => $company_id]);

        if ($this->type) {
            $query->where($this->type, '=', $value);
        }

        if ($id) {
            $query->where('id', '!=', $id);
        }
        if ($query->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('message.data_exits');
    }
}
