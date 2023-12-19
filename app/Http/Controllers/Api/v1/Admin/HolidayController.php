<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Requests\Admin\StoreOrUpdateHolidayRequest;
use App\Http\Services\v1\Admin\HolidayService;
use Illuminate\Http\Request;

class HolidayController extends BaseMasterController
{
    /**
     * @param HolidayService $holidayService
     */
    public function __construct(HolidayService $holidayService)
    {
        $this->service = $holidayService;
    }

    /**
     * @param StoreOrUpdateHolidayRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreOrUpdateHolidayRequest $request)
    {
        $this->service->addCompanyToRequest();

        return $this->service->_store($request);
    }

    /**
     * @param StoreOrUpdateHolidayRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function update(StoreOrUpdateHolidayRequest $request, $id)
    {
        return $this->service->_update($request, $id);
    }
}
