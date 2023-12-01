<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOrUpdateIdenCardEmployeeRequest;
use App\Http\Services\v1\User\IdentificationService;
use App\Http\Services\v1\Admin\IdentificationCardService;
use Illuminate\Http\Request;

class IdentificationController extends Controller
{
    /**
     * @param IdentificationService $identificationService
     */
    /**
     * @param IdentificationService $identificationAdminService
     */

    public function __construct(IdentificationService $identificationService, IdentificationCardService $identificationAdminService)
    {
        $this->service = $identificationService;
        $this ->identificationAdminService= $identificationAdminService;
    }

    public function identificationCard()
    {
        return $this->service->identificationCard();
    }

    public function updateIdentification(Request $request)
    {
        return $this->service->updateIdentification($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        return $this->service->_store($request);
    }

    /**
     * @param StoreOrUpdateIdenCardEmployeeRequest $request
     * @param $employee_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateByEmployee(StoreOrUpdateIdenCardEmployeeRequest $request, $employee_id)
    {
        return $this->identificationAdminService->updateByEmployee($request, $employee_id);
    }

    /**
     * @param Request $request
     * @param $employee_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function byEmployee(Request $request, $employee_id)
    {
        return $this->identificationAdminService->getByEmployee($employee_id);
    }
}
