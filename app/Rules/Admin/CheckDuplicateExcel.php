<?php

namespace App\Rules\Admin;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;

class CheckDuplicateExcel implements Rule
{
    public $rows;
    public $heads;

    public function __construct($rows, $heads)
    {
        $this->rows = $rows;
        $this->heads = $heads;
    }

    public function passes($attribute, $value): bool
    {
        $isDuplicate = $this->rows->where($this->heads, $value)->count();

        if ($isDuplicate > 1) {
            return false;
        }

        return true;
    }

    public function message()
    {
        return __('message.excel_exits');
    }
}
