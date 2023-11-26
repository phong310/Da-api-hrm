<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Http\Services\v1\User\BankAccountService;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    /**
     * @param BankAccountService $bankAccountService
     */
    public function __construct(BankAccountService $bankAccountService)
    {
        $this->service = $bankAccountService;
    }
    public function banking()
    {
        return $this->service->banking();
    }

    public function updateBanking(Request $request)
    {
        return $this->service->updateBanking($request);
    }
}
