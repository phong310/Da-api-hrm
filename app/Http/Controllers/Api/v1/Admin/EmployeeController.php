<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateEmployeeRequest;
use App\Http\Services\v1\Admin\EmployeeService;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    //
    /**
     * @var EmployeeService
     */
    protected $employeeService;

    /**
     * Instantiate a new controller instance.
     *
     * @param EmployeeService $employeeService
     */
    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        return $this->employeeService->index($request);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Http\JsonResponse|mixed|object|null
     */
    public function show(Request $request, $id)
    {
        return $this->employeeService->show($request, $id);
    }

    /**
     * @param CreateEmployeeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateEmployeeRequest $request)
    {
        return $this->employeeService->store($request);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        return $this->employeeService->update($request, $id);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        return $this->employeeService->destroy($request, $id);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function import(Request $request)
    {
        return $this->employeeService->import($request);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    { 
        return $this->employeeService->export();
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportTemplate(Request $request)
    {
        return $this->employeeService->exportTemplate($request);
    }

    public function superCreateEmployee(CreateEmployeeRequest $request)
    {
        return $this->employeeService->superCreateEmployee($request);
    }
}
