<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Api\v1\BaseController;
use App\Http\Requests\Admin\StoreOrUpdateDepartmentRequest;
use App\Http\Services\v1\Admin\DepartmentService;

class DepartmentController extends BaseController
{
    /**
     * Instantiate a new controller instance.
     *
     * @param DepartmentService $departmentService
     */
    public function __construct(DepartmentService $departmentService)
    {
        $this->service = $departmentService;
    }

    /**
     * @param StoreOrUpdateDepartmentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreOrUpdateDepartmentRequest $request)
    {
        $this->service->addCompanyToRequest();

        return $this->service->_store($request);
    }

    /**
     * @param StoreOrUpdateDepartmentRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function update(StoreOrUpdateDepartmentRequest $request, $id)
    {
        return $this->service->_update($request, $id);
    }
}
