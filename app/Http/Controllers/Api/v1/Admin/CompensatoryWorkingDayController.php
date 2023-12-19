<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Requests\Admin\StoreOrUpdateCompensatoryWDRequest;
use App\Http\Services\v1\Admin\CompensatoryWorkingDayService;

class CompensatoryWorkingDayController extends BaseMasterController
{
    public function __construct(CompensatoryWorkingDayService $compensatoryWorkingDayService)
    {
        $this->service = $compensatoryWorkingDayService;
    }

    /**
     * @param StoreOrUpdateCompensatoryWDRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreOrUpdateCompensatoryWDRequest $request)
    {
        $this->service->addCompanyToRequest();

        return $this->service->store($request);
    }

    /**
     * @param StoreOrUpdateCompensatoryWDRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function update(StoreOrUpdateCompensatoryWDRequest $request, $id)
    {
        return $this->service->update($request, $id);
    }
}
