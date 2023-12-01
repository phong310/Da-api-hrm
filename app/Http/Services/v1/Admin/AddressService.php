<?php

namespace App\Http\Services\v1\Admin;

use App\Http\Services\v1\BaseService;
use App\Models\Address;
use App\Models\Employee;
use Illuminate\Http\Request;

class AddressService extends BaseService
{
    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new Address();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = $this->addDefaultFilter();

        return response()->json($data);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|mixed|void
     */
    public function show(Request $request, $id)
    {
    }

    /**
     * @param Request $request
     * @param $id
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * @param Request $request
     * @param $id
     * @param false $isForceDelete
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id, $isForceDelete = false)
    {
        $instance = $this->query->findOrFail($id);
        $instance->delete();

        return response()->json(['message' => __('message.delete_success')]);
    }

    /**
     * @param $employee_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByEmployee($employee_id)
    {
        $address = $this->query->whereHas('personalInformation', function ($q) use ($employee_id) {
            $q->whereHas('employee', function ($q) use ($employee_id) {
                $q->where(['employees.id' => $employee_id]);
            });
        })->get();

        return response()->json($address);
    }

    /**
     * @param Request $request
     * @param $employee_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateByEmployee(Request $request, $employee_id)
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
