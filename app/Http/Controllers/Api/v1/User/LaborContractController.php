<?php

namespace App\Http\Controllers\Api\v1\User;


use App\Http\Controllers\Api\v1\BaseController;
use App\Http\Requests\User\StoreOrUpdateLaborContractRequest;
use App\Http\Services\v1\User\LaborContract\LaborContractAddressService;
use App\Http\Services\v1\User\LaborContract\LaborContractHasAllowanceService;
use App\Http\Services\v1\User\LaborContract\LaborContractService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaborContractController extends BaseController
{
    /**
     * @var LaborContractAddressService
     */
    protected $laborContractAddressService;
    /**
     * @var LaborContractHasAllowanceService
     */
    protected $laborContractHasAllowanceService;

    /**
     * @param LaborContractService $laborContractService
     * @param LaborContractAddressService $laborContractAddressService
     * @param LaborContractHasAllowanceService $laborContractHasAllowanceService
     */

    public function __construct(
        LaborContractService $laborContractService,
        LaborContractAddressService $laborContractAddressService,
        LaborContractHasAllowanceService $laborContractHasAllowanceService
    ) {
        $this->service = $laborContractService;
        $this->laborContractAddressService = $laborContractAddressService;
        $this->laborContractHasAllowanceService = $laborContractHasAllowanceService;
    }

    /**
     * @param StoreOrUpdateLaborContractRequest $request
     * @return \Illuminate\Http\JsonResponse
     */


    public function store(StoreOrUpdateLaborContractRequest $request)
    {
        try {
            DB::beginTransaction();
            $laborContract = $this->service->store($request);

            $this->laborContractHasAllowanceService->storeArray($request->allowances, $laborContract->id);

            DB::commit();

            return response()->json([
                'message' => __('message.created_success')
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(Request $request, $id)
    {

        return $this->service->show($request, $id);
    }

    public function update(StoreOrUpdateLaborContractRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $laborContract = $this->service->update($request, $id);

            $this->laborContractHasAllowanceService->updateArray($request->allowances, $laborContract->id);

            DB::commit();

            return response()->json([
                'message' => __('message.update_success')
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getEmployeeLabor(Request $request)
    {
        $laborContracts = $this->service->getLaborContractMySelf($request);

        return response()->json($laborContracts);
    }

    public function showMySelf(Request $request, $id)
    {
        $laborContract = $this->service->showMySelf($request, $id);
        if (!$laborContract) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json($laborContract);
    }

    public function countByEmployee($employeeId)
    {
        return $this->service->countByEmployee($employeeId);
    }

    public function hasLaborContractActive($employeeId)
    {
        return $this->service->hasLaborContractActive($employeeId);
    }
}
