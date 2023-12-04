<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Api\v1\BaseController;
use App\Http\Requests\User\TimekeepingRequest;
use App\Http\Services\v1\User\TimeKeepingService;
use Illuminate\Http\Request;

class TimeKeepingController extends BaseController
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(TimeKeepingService $timeKeepingService)
    {
        $this->service = $timeKeepingService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function todayTimeSheetLog(Request $request)
    {
        return $this->service->todayTimeSheetLog($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function totalTimeInMonth(Request $request)
    {
        return $this->service->getTotalTimeInMonth($request);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function amount(Request $request)
    {
        return $this->service->amount($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        return $this->service->store($request);
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

    public function checkHasTimekeeping(Request $request)
    {
        return $this->service->checkHasTimekeeping($request);
    }
}
