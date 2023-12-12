<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Api\v1\BaseController;
use App\Http\Services\v1\User\HolidayService;

class HolidayController extends BaseController
{
    /**
     * @param HolidayService $holidayService
     */
    public function __construct(HolidayService $holidayService)
    {
        $this->service = $holidayService;
    }
}
