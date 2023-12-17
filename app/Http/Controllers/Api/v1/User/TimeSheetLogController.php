<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Api\v1\BaseController;
use App\Http\Services\v1\User\TimeSheetLogService;
use Illuminate\Http\Request;

class TimeSheetLogController extends BaseController
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(TimeSheetLogService $tslService)
    {
        $this->service = $tslService;
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

    /**
     * @param $month
     * @return \Illuminate\Http\JsonResponse
     */
    public function employeesByMonth(Request $request, $month)
    {
        $perPage = $request->get('per_page');
        $employeeName = $request->get('employee_name');

        return $this->service->employeesByMonth($month, $perPage, $employeeName);
    }
}
