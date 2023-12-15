<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Api\v1\BaseController;
use App\Http\Services\v1\User\TimeSheetService;
use Illuminate\Http\Request;

class TimeSheetController extends BaseController
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(TimeSheetService $service)
    {
        $this->service = $service;
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
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function update(Request $request, $id)
    {
        return $this->service->_update($request, $id);
    }

    public function employeesByMonth(Request $request, $month)
    {
        $perPage = $request->get('per_page');
        $employeeName = $request->get('employee_name');

        return $this->service->employeesByMonth($month, $perPage, $employeeName);
    }

    public function checkHasFormByDate($date)
    {
        return $this->service->checkHasFormByDate($date);
    }
}
