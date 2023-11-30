<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Requests\Admin\StoreOrUpdateTitleRequest;
use App\Http\Services\v1\Admin\TitleService;

class TitleController extends BaseMasterController
{
    /**
     * Instantiate a new controller instance.
     *
     * @param TitleService $titleService
     */
    public function __construct(TitleService $titleService)
    {
        $this->service = $titleService;
    }

    /**
     * @param StoreOrUpdateTitleRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreOrUpdateTitleRequest $request)
    {
        $this->service->addCompanyToRequest();

        return $this->service->_store($request);
    }

    /**
     * @param StoreOrUpdateTitleRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function update(StoreOrUpdateTitleRequest $request, $id)
    {
        return $this->service->_update($request, $id);
    }

    // public function destroyTitle($id)
    // {
    //     return $this->service->destroys($id);
    // }
}
