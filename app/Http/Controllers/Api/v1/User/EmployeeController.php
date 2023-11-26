<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Api\v1\BaseController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateEmployeeRequest;
use App\Http\Requests\Admin\UpdateEmployeeRequest;
use App\Http\Services\v1\User\EmployeeService;
use App\Http\Services\v1\Admin\EmployeeService as EmployeeAdminService;
use Illuminate\Http\Request;

class EmployeeController extends BaseController
{
    /**
     * @var EmployeeService
     */
    protected $employeeUserService;
    protected $employeeAdminService;
    /**/

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeUserService = $employeeService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        return $this->employeeUserService->index($request);
    }

    /**
     * @param Request $request
     * @param $employee_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function info(Request $request, $employee_id)
    {
        return $this->employeeUserService->info($request, $employee_id);
    }

    /**
     * @param Request $request
     * @param $employee_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateInfo(Request $request, $employee_id)
    {
        return $this->employeeUserService->updateInfo($request, $employee_id);
    }

    public function getListByCompany()
    {
        return $this->employeeUserService->getListByCompany();
    }
}
