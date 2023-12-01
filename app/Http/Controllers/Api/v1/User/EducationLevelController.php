<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Api\v1\BaseController;
use App\Http\Requests\Admin\StoreOrUpdateEducationLevelRequest;
use App\Http\Services\v1\Admin\EducationLevelService;

class EducationLevelController extends BaseController
{
    /**
     * Instantiate a new controller instance.
     *
     * @param EducationLevelService $educationLevelService
     */
    public function __construct(EducationLevelService $educationLevelService)
    {
        $this->service = $educationLevelService;
    }

    /**
     * @param StoreOrUpdateEducationLevelRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreOrUpdateEducationLevelRequest $request)
    {
        $this->service->addCompanyToRequest();

        return $this->service->_store($request);
    }

    /**
     * @param StoreOrUpdateEducationLevelRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function update(StoreOrUpdateEducationLevelRequest $request, $id)
    {
        return $this->service->_update($request, $id);
    }
}
