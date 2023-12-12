<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Api\v1\Admin\BaseMasterController;
use App\Http\Services\v1\User\CompensatoryWorkingDayService;

class CompensatoryWorkingDayController extends BaseMasterController
{
    public function __construct(CompensatoryWorkingDayService $compensatoryWorkingDayService)
    {
        $this->service = $compensatoryWorkingDayService;
    }
}
