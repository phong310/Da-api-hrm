<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Api\v1\BaseController;
use App\Http\Requests\Admin\StoreOrUpdatePositionRequest;
use App\Http\Services\v1\Admin\PositionService;

class PositionController extends BaseController
{
    /**
     * Instantiate a new controller instance.
     *
     * @param PositionService $positionService
     */
    public function __construct(PositionService $positionService)
    {
        $this->service = $positionService;
    }

    /**
     * @param StoreOrUpdatePositionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreOrUpdatePositionRequest $request)
    {
        $this->service->addCompanyToRequest();

        return $this->service->_store($request);
    }

    /**
     * @param StoreOrUpdatePositionRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function update(StoreOrUpdatePositionRequest $request, $id)
    {
        return $this->service->_update($request, $id);
    }
}
