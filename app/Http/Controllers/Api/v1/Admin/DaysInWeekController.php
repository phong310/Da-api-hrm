<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Services\v1\Admin\DaysInWeekService;
use Illuminate\Http\Request;

class DaysInWeekController extends BaseMasterController
{
    /**
     * @param DaysInWeekService $daysInWeekService
     */
    public function __construct(DaysInWeekService $daysInWeekService)
    {
        $this->service = $daysInWeekService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $this->service->addCompanyToRequest();

        return $this->service->_store($request);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function update(Request $request, $id)
    {
        return $this->service->_update($request, $id);
    }
}
