<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\v1\Admin\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * @var RoleService
     */
    protected $roleService;

    /**
     * Instantiate a new controller instance.
     *
     * @param RoleService $roleService
     */
    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        return $this->roleService->index($request);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Http\JsonResponse|mixed
     */
    public function show(Request $request, $id)
    {
        return $this->roleService->show($request, $id);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        return $this->roleService->store($request);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        return $this->roleService->update($request, $id);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        return $this->roleService->destroy($id);
    }
}
