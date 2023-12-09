<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Requests\Admin\StoreWorkingDayRequest;
use App\Http\Services\v1\Admin\WorkingDayService;

class WorkingDayController extends BaseMasterController
{
    /**
     * @param WorkingDayService $workingDayService
     */
    public function __construct(WorkingDayService $workingDayService)
    {
        $this->service = $workingDayService;
    }

    /**
     * @param StoreWorkingDayRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreWorkingDayRequest $request)
    {
        $this->service->addCompanyToRequest();

        return $this->service->_store($request);
    }

    /**
     * @param StoreWorkingDayRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeMulti(StoreWorkingDayRequest $request)
    {
        return $this->service->_storeMulti($request);
    }

    /**
     * @param StoreWorkingDayRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function update(StoreWorkingDayRequest $request, $id)
    {
        return $this->service->_update($request, $id);
    }
}
