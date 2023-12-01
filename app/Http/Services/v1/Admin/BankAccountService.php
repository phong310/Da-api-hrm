<?php

namespace App\Http\Services\v1\Admin;

use App\Models\BankAccount;

class BankAccountService extends BaseMasterService
{
    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new BankAccount();
    }

    /**
     * @param $employee_id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getByEmployee($employee_id)
    {
        return $this->query->where(['employee_id' => $employee_id])->first();
    }

    /**
     * @param $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateOrCreateByEmployee($request, $id)
    {
        $data = $request->all();
        try {
            if (
                !$data['account_name'] &&
                !$data['account_number'] &&
                !$data['bank_branch'] &&
                !$data['bank_name'] &&
                !$data['bank_type']
            ) {
                BankAccount::where(['employee_id' => $data['employee_id']])->delete();
            } else {
                BankAccount::updateOrCreate(['employee_id' => $data['employee_id']], $data);
            }

            return response()->json([
                'message' => __('message.update_success'),
                'data' => $data,
            ]);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }
}
