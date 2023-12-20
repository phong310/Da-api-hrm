<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Services\v1\Admin\OtherAllowanceService;
use App\Http\Requests\Admin\StoreOrUpdateOtherAllowanceRequest;
use Illuminate\Http\Request;

class OtherAllowanceController extends BaseMasterController
{

    protected $service;
    /**
     * @param OtherAllowanceService $allowanceService
     */
    public function __construct(OtherAllowanceService $allowanceService)
    {
        $this->service = $allowanceService;
    }

    public function store(StoreOrUpdateOtherAllowanceRequest $request)
    {
        $this->service->store($request);
        return response()->json(['message' => __('message.created_success')]);
    }

    public function update(StoreOrUpdateOtherAllowanceRequest $request, $id)
    {
        $this->service->update($request, $id);
        return response()->json(['message' => __('message.update_success')]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        return $this->service->destroy($request, $id);
    }
}
