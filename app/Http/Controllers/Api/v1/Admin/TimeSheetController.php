<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Api\v1\BaseController;
use App\Http\Services\v1\Admin\TimeSheetService;
use Illuminate\Http\Request;

class TimeSheetController extends BaseController
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(TimeSheetService $timeSheetService)
    {
        $this->service = $timeSheetService;
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
