<?php

namespace App\Http\Services\v1\User;


use Illuminate\Http\Request;
use App\Http\Services\v1\BaseService;
use App\Models\BankAccount;
use App\Models\Employee;
use App\Models\User;

class BankAccountService extends BaseService
{
    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new BankAccount();
    }


    public function banking()
    {
        $userBanking = $this->query->where('employee_id', request()->user()->employee_id)->first();
        return response()->json($userBanking);
    }

    /**
     * @param Request $request
     * @param $employee_id
     * @return \Illuminate\Http\JsonResponse
     */

    public function updateBanking(Request $request)
    {
        $data = $request->all();
        $user = $request->user();
        $bank_accounts = BankAccount::updateOrCreate(['employee_id' => $user->employee_id], $data);
        return response()->json([
            'message' => __('message.update_success'),
        ], 200);
    }
}
