<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOrUpdateEducationEmployeeRequest;
use App\Http\Services\v1\Admin\EducationService;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    //
    /**
     * @var EducationService
     */
    protected $educationService;

    /**
     * @param EducationService $educationService
     */
    public function __construct(EducationService $educationService)
    {
        $this->educationService = $educationService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        return $this->educationService->index($request);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|mixed|void
     */
    public function show(Request $request, $id)
    {
        return $this->educationService->show($request, $id);
    }

    /**
     * @param Request $request
     */
    public function store(Request $request)
    {
        return $this->educationService->store($request);
    }

    /**
     * @param Request $request
     * @param $id
     */
    public function update(Request $request, $id)
    {
        return $this->educationService->update($request, $id);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        return $this->educationService->destroy($request, $id);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createbyEmployee(Request $request)
    {
        return $this->educationService->createByEmployee($request);
    }

    /**
     * @param Request $request
     * @param $employee_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function byEmployee(Request $request, $employee_id)
    {
        return $this->educationService->getByEmployee($employee_id);
    }

    /**
     * @param StoreOrUpdateEducationEmployeeRequest $request
     * @param $employee_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateByEmployee(StoreOrUpdateEducationEmployeeRequest $request, $employee_id)
    {
        return $this->educationService->updateByEmployee($request, $employee_id);
    }

    /**
     * @param Request $request
     * @param $employee_id
     * @param $education_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteByEmployee(Request $request, $employee_id, $education_id)
    {
        return $this->educationService->deleteByEmployee($request, $employee_id, $education_id);
    }
}
