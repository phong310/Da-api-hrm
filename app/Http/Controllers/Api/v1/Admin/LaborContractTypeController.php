<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Requests\Admin\StoreOrUpdateLaborContractTypeRequest;
use App\Http\Requests\Admin\StoreOrUpdateLindOfLeaveRequest;
use App\Http\Services\v1\Admin\LaborContract\LaborContractTypeHasAllowanceService;
use App\Http\Services\v1\Admin\LaborContract\LaborContractTypeService;
use Illuminate\Support\Facades\DB;

class LaborContractTypeController extends BaseMasterController
{

    /**
     * @var LaborContractTypeHasAllowanceService
     */
    protected $laborContractTypeHasAllowanceService;

    public function __construct(
        LaborContractTypeService             $laborContractTypeService,
        LaborContractTypeHasAllowanceService $laborContractTypeHasAllowanceService
    ) {
        $this->service = $laborContractTypeService;
        $this->laborContractTypeHasAllowanceService = $laborContractTypeHasAllowanceService;
    }

    /**
     * @param StoreOrUpdateLindOfLeaveRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreOrUpdateLaborContractTypeRequest $request)
    {
        try {
            DB::beginTransaction();
            $laborContractType = $this->service->store($request);

            $this->laborContractTypeHasAllowanceService->storeArray($request->allowances, $laborContractType->id);

            DB::commit();

            return response()->json([
                'message' => __('message.created_success')
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @param StoreOrUpdateLindOfLeaveRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function update(StoreOrUpdateLaborContractTypeRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $laborContractType = $this->service->update($request, $id);

            $this->laborContractTypeHasAllowanceService->updateArray($request->allowances, $laborContractType->id);

            DB::commit();

            return response()->json([
                'message' => __('message.update_success')
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
