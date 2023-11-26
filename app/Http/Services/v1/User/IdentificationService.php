<?php

namespace App\Http\Services\v1\User;


use Illuminate\Http\Request;
use App\Http\Services\v1\BaseService;
use App\Models\BankAccount;
use App\Models\Employee;
use App\Models\IdentificationCard;
use App\Models\User;

class IdentificationService extends BaseService
{
    /**
     * @return mixed|void
     */
    public function setModel()
    {

        $this->model = new IdentificationCard();
    }

    public function identificationCard()
    {
        $employee_id = request()->user()->employee_id;
        $employee = Employee::where(['id' => $employee_id])->with('information', 'information.identificationCards')->first();
        return response()->json(count($employee->information->identificationCards) ? $employee->information->identificationCards[0] : (object)[]);
    }

    /**
     * @param Request $request
     * @param $employee_id
     * @return \Illuminate\Http\JsonResponse
     */

    public function updateIdentification(Request $request)
    {
        $data = $request->all();
        $employee_id = request()->user()->employee_id;
        $employee = Employee::where(['id' => $employee_id])->first()->personal_information_id;
        $identification = IdentificationCard::updateOrCreate(['personal_information_id' => $employee], $data);
        return response()->json([
            'message' => __('message.update_success'),
        ], 200);
    }
}
