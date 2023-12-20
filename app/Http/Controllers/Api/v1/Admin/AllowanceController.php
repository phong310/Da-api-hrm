<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Requests\Admin\StoreOrUpdateAllowanceRequest;
use App\Http\Requests\Admin\StoreOrUpdateLindOfLeaveRequest;
use App\Http\Services\v1\Admin\LaborContract\AllowanceService;

class AllowanceController extends BaseMasterController
{
    /**
     * @param AllowanceService $allowanceService
     */
    public function __construct(AllowanceService $allowanceService)
    {
        $this->service = $allowanceService;
    }

    /**
     * @param StoreOrUpdateLindOfLeaveRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreOrUpdateAllowanceRequest $request)
    {
        $this->service->addCompanyToRequest();

        return $this->service->_store($request);
    }

    /**
     * @param StoreOrUpdateLindOfLeaveRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function update(StoreOrUpdateAllowanceRequest $request, $id)
    {
        return $this->service->_update($request, $id);
    }
}
