<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Api\v1\BaseController;
use App\Http\Services\v1\Admin\TimeOffService;
use App\Http\Services\v1\User\NumberOfDaysOffService;
use Illuminate\Http\Request;

class NumberOfDaysOffUserController extends BaseController
{
    /**
     * Instantiate a new controller instance.
     *
     * @param NumberOfDaysOffService $numberOfDaysOffService
     */
    /**
     * Instantiate a new controller instance.
     *
     * @param TimeOffService $timeOffService
     */

    public function __construct(NumberOfDaysOffService $numberOfDaysOffService, TimeOffService $timeOffService)
    {
        $this->service = $numberOfDaysOffService;
        $this->timeOffService = $timeOffService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function remainingDaysOff(Request $request)
    {
        $employee_id = $request->employee_id;
        $data = $this->service->getNumberOfDaysOffOfEmployee($employee_id);

        return response()->json($data);
    }

    public function byEmployee($employee_id)
    {
        return $this->timeOffService->getByEmployee($employee_id);
    }

    public function store(Request $request)
    {
        $this->timeOffService->addCompanyToRequest();
        return $this->timeOffService->store($request);
    }
}
