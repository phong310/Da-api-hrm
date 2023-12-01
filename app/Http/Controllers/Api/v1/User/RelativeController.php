<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUpdateRelativeEmployee;
use App\Http\Services\v1\Admin\RelativeService;
use Illuminate\Http\Request;

class RelativeController extends Controller
{
    /**
     * @param RelativeService $relativesService
     */
    public function __construct(RelativeService $relativesService)
    {
        $this->service = $relativesService;
    }


    /**
     * @param Request $request
     * @param $employee_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function byEmployee(Request $request, $employee_id)
    {
        return $this->service->getByEmployee($employee_id);
    }

    /**

     * @param $relative_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateByEmployee(StoreUpdateRelativeEmployee $request, $relative_id)
    {
        return $this->service->updateByEmployee($request, $relative_id);
    }

    public function createByEmployee(Request $request)
    {
        return $this->service->createByEmployee($request);
    }

    public function deleteByEmployee(Request $request, $employee_id, $relative_id)
    {
        return $this->service->deleteByEmployee($request, $employee_id, $relative_id);
    }
}
