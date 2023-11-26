<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Api\v1\Admin\BaseMasterController;
use App\Http\Requests\User\StoreWorkingDayUserRequest;
use App\Http\Services\v1\User\WorkingDayUserService;
use Illuminate\Http\Request;

class WorkingDayUserController extends BaseMasterController
{
    /**
     * @param WorkingDayUserService $workingDayUserService
     */
    public function __construct(WorkingDayUserService $workingDayUserService)
    {
        $this->service = $workingDayUserService;
    }

    /**
     * @param StoreWorkingDayUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $this->service->addCompanyToRequest();

        return $this->service->_store($request, $message = '');
    }

    /**
     * @param StoreWorkingDayUserRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function update(Request $request, $id)
    {
        return $this->service->_update($request, $id);
    }
}
