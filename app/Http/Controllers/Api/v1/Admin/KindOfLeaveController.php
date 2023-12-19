<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Requests\Admin\StoreOrUpdateLindOfLeaveRequest;
use App\Http\Services\v1\Admin\KindOfLeaveService;

class KindOfLeaveController extends BaseMasterController
{
    /**
     * @param KindOfLeaveService $kindOfLeaveService
     */
    public function __construct(KindOfLeaveService $kindOfLeaveService)
    {
        $this->service = $kindOfLeaveService;
    }

    /**
     * @param StoreOrUpdateLindOfLeaveRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreOrUpdateLindOfLeaveRequest $request)
    {
        $this->service->addCompanyToRequest();

        return $this->service->_store($request);
    }

    /**
     * @param StoreOrUpdateLindOfLeaveRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function update(StoreOrUpdateLindOfLeaveRequest $request, $id)
    {
        return $this->service->_update($request, $id);
    }
}
