<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Api\v1\BaseController;
use App\Http\Services\v1\User\LaborContract\AllowanceService;

class AllowanceController extends BaseController
{
    /**
     * @param AllowanceService $allowanceService
     */

    public function __construct(AllowanceService $allowanceService)
    {
        $this->service = $allowanceService;
    }
}
