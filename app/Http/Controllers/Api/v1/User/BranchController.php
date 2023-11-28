<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Api\v1\BaseController;
use App\Http\Requests\Admin\StoreOrUpdateBranchRequest;
use App\Http\Services\v1\Admin\BranchService;

class BranchController extends BaseController
{
    /**
     * Instantiate a new controller instance.
     *
     * @param BranchService $branchService
     */
    public function __construct(BranchService $branchService)
    {
        $this->service = $branchService;
    }

    /**
     * @param StoreOrUpdateBranchRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreOrUpdateBranchRequest $request)
    {
        $this->service->addCompanyToRequest();

        return $this->service->_store($request);
    }

    /**
     * @param StoreOrUpdateBranchRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function update(StoreOrUpdateBranchRequest $request, $id)
    {
        return $this->service->_update($request, $id);
    }
}
