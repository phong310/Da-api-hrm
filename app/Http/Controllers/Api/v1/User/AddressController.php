<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAddressEmployeeRequest;
use App\Http\Services\v1\User\AddressService;
use Illuminate\Http\Request;

class AddressController extends Controller
{

    public function __construct(AddressService $addressService)
    {
        $this->service = $addressService;
    }

    public function address()
    {
        return $this->service->address();
    }

    public function updateAddress(StoreAddressEmployeeRequest $request, $employee_id)
    {
        return $this->service->updateAddress($request, $employee_id);
    }
}
