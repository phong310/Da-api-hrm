<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Requests\Admin\CompanyRequest;
use App\Http\Requests\Admin\CreateAccountRequest;
use App\Http\Requests\Admin\DepartmentBranchRequest;
use App\Http\Services\v1\Admin\BranchService;
use App\Http\Services\v1\Admin\CompanyService;
use App\Http\Services\v1\Admin\DepartmentService;
use App\Http\Services\v1\Admin\RoleService;
use App\Http\Services\v1\Admin\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class CompanyController extends BaseMasterController
{
    /**
     * @var BranchService
     */
    protected $branchService;
    /**
     * @var DepartmentService
     */
    protected $departmentService;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var RoleService
     */
    protected $roleService;

    /**
     * @param CompanyService $companyService
     */
    public function __construct(
        CompanyService $companyService,
        BranchService $branchService,
        DepartmentService $departmentService,
        RoleService $roleService,
        UserService $userService
    ) {
        $this->service = $companyService;
        $this->branchService = $branchService;
        $this->departmentService = $departmentService;
        $this->roleService = $roleService;
        $this->userService = $userService;
    }

    /**
     * @param CompanyRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CompanyRequest $request)
    {
        return $this->service->store($request);
    }

    /**
     * @param CompanyRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CompanyRequest $request, $id)
    {
        return $this->service->update($request, $id);
    }

    public function updateInfo(CompanyRequest $request, $company_id)
    {
        return $this->service->updateInfo($request, $company_id);
    }

    /**
     * @param DepartmentBranchRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function departmentBranch(DepartmentBranchRequest $request, $id)
    {

        $branchs = $request->get('branchs');
        $departments = $request->get('departments');

        $this->branchService->storeMulti($branchs, $id);
        $this->departmentService->storeMulti($departments, $id);

        return response()->json([
            'message' => __('message.created_success'),
            'status' => Response::HTTP_OK,
        ]);
    }

    /**
     * @param CreateAccountRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function createAccounts(CreateAccountRequest $request, $id)
    {
        try {
            $accounts = $request->get('accounts');
            $this->roleService->storeRoleDefault($id);
            $this->userService->storeMulti($accounts, $id);

            return response()->json([
                'message' => __('message.created_success'),
                'status' => Response::HTTP_OK,
            ]);
        } catch (\Exception $exception) {
            Log::info($exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore(Request $request, $id)
    {
        $result = $this->service->restore($request, $id);

        return $result;
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        return $this->service->destroy($request, $id);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function forceDestroy(Request $request, $id)
    {
        return $this->service->destroy($request, $id, true);
    }
}
