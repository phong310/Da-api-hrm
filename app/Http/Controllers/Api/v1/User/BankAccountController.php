<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Http\Services\v1\User\BankAccountService;
use Illuminate\Http\Request;
use App\Http\Services\v1\Admin\BankAccountService as BankAccountAdminService;

class BankAccountController extends Controller
{
    /**
     * @param BankAccountService $bankAccountService
     */
    /**
     *  @param BankAccountAdminService $bankAccountAdminService
     */
    public function __construct(BankAccountService $bankAccountService, BankAccountAdminService $bankAccountAdminService)
    {
        $this->service = $bankAccountService;
        $this->bankAccountAdminService = $bankAccountAdminService;
    }
    public function banking()
    {
        return $this->service->banking();
    }

    public function updateBanking(Request $request)
    {
        return $this->service->updateBanking($request);
    }

    /**
     * @param Request $request
     * @param $employee_id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function byEmployee(Request $request, $employee_id)
    {
        return $this->bankAccountAdminService->getByEmployee($employee_id);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateByEmployee(Request $request, $id)
    {
        return $this->bankAccountAdminService->updateOrCreateByEmployee($request, $id);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeByEmployee(Request $request)
    {
        $id = null;

        return $this->bankAccountAdminService->updateOrCreateByEmployee($request, $id);
    }
}
