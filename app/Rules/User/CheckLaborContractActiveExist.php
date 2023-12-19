<?php

namespace App\Rules\User;

use App\Models\LaborContract\LaborContract;
use App\Repositories\Interfaces\LaborContract\LaborContractInterface;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Traits\SystemSetting;

class CheckLaborContractActiveExist implements Rule
{
    protected $contract;

    public function __construct(LaborContractInterface $contract)
    {
        $this->contract = $contract;
    }

    public function passes($attribute, $value)
    {
        try {
            $user = Auth::user();
            $company_id = $user->company_id;
            $id = request()->id;
            $query = $this->contract->queryValidateUnique($company_id, $value);
            if ($id) {
                $query->where('id', '<>', $id);
            }
            return !$query->exists();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function message()
    {
        return __('message.labor_contract_exits');
    }
}
