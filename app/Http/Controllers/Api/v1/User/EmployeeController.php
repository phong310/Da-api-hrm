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
    /**
     * Instantiate a new controller instance.
     *
     * @param EmployeeService $employeeUserService
     */
    public function __construct(EmployeeService $employeeService, EmployeeAdminService $employeeAdminService)
    {
        $this->employeeUserService = $employeeService;
        $this->employeeAdminService = $employeeAdminService;
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
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Http\JsonResponse|mixed|object|null
     */
    public function show(Request $request, $id)
    {
        return $this->employeeAdminService->show($request, $id);
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

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function update(UpdateEmployeeRequest $request, $id)
    {
        return $this->employeeAdminService->update($request, $id);
    }

    public function store(CreateEmployeeRequest $request)
    {
        return $this->employeeAdminService->store($request);
    }

    public function getListByCompany()
    {
        return $this->employeeUserService->getListByCompany();
    }
}
