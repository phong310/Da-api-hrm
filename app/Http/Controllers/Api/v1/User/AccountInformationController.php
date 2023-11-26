<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Api\v1\BaseController;
use App\Http\Requests\Admin\StoreOrUpdateAccountInformationEmployeeRequest;
use App\Http\Services\v1\Admin\AccountInformationService;
use Illuminate\Http\Request;

class AccountInformationController extends BaseController
{
    /**
     * Instantiate a new controller instance.
     *
     * @param AccountInformationService $bankAccoutServicen
     */
    public function __construct(AccountInformationService $accountInformationService)
    {
        $this->service = $accountInformationService;
    }

    /**
     * @param Request $request
     * @param $employee_id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function byEmployee(Request $request, $employee_id)
    {
        return $this->service->getByEmployee($employee_id);
    }


    /**
     * @param StoreOrUpdateAccountInformationEmployeeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createByEmployee(StoreOrUpdateAccountInformationEmployeeRequest $request)
    {
        return $this->service->createByEmployee($request);
    }


    /**
     * @param StoreOrUpdateAccountInformationEmployeeRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateByEmployee(StoreOrUpdateAccountInformationEmployeeRequest $request, $id)
    {
        return $this->service->updateByEmployee($request, $id);
    }
}
