<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAddressEmployeeRequest;
use App\Http\Services\v1\User\AddressService;
use App\Http\Services\v1\Admin\AddressService as AddressAdminService;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * @param AddressService $addressService
     */
    /**
     * @param AddressAdminService $addressAdminService
     */

    public function __construct(AddressService $addressService, AddressAdminService $addressAdminService)
    {
        $this->service = $addressService;
        $this->addressAdminService = $addressAdminService;
    }

    public function address()
    {
        return $this->service->address();
    }

    public function updateAddress(StoreAddressEmployeeRequest $request, $employee_id)
    {
        return $this->service->updateAddress($request, $employee_id);
    }

    /**
     * @param Request $request
     * @param $employee_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function byEmployee(Request $request, $employee_id)
    {
        return $this->addressAdminService->getByEmployee($employee_id);
    }

    /**
     * @param StoreAddressEmployeeRequest $request
     * @param $employee_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateByEmployee(StoreAddressEmployeeRequest $request, $employee_id)
    {
        return $this->addressAdminService->updateByEmployee($request, $employee_id);
    }
}
