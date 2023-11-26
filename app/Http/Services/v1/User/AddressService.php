<?php

namespace App\Http\Services\v1\User;


use Illuminate\Http\Request;
use App\Http\Services\v1\BaseService;
use App\Models\Address;
use App\Models\BankAccount;
use App\Models\Employee;
use App\Models\User;

class AddressService extends BaseService
{
    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new Address();
    }

    public function address()
    {
        $employee_id = request()->user()->employee_id;
        $employee = Employee::where(['id' => $employee_id])->with('information', 'information.addresses')->first();
        return response()->json($employee->information->addresses);
    }

    public function updateAddress(Request $request, $employee_id)
    {
        $data = $request->all();
        $employee = Employee::query()->where(['id' => $employee_id])->first();
        $addresses = [$data['RESIDENT'], $data['DOMICILE']];
        try {
            if (!count($addresses) || !$employee) {
                return response()->json([
                    'message' => __('message.not_found'),
                ], 404);
            }

            foreach ($addresses as $address) {
                unset($address['personal_information_id']);
                $address['personal_information_id'] = $employee->personal_information_id;

                $data = [
                    'type' => $address['type'],
                    'personal_information_id' => $address['personal_information_id']
                ];

                if (
                    !$address['province'] &&
                    !$address['district'] &&
                    !$address['ward'] &&
                    !$address['address']
                ) {
                    Address::where($data)->delete();
                } else {
                    Address::updateOrCreate($data, $address);
                }
            }

            return response()->json([
                'message' => __('message.update_success'),
            ]);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }
}
