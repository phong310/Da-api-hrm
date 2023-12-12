<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Api\v1\BaseController;
use App\Http\Services\v1\Admin\KindOfLeaveService;
use Illuminate\Http\Request;

class KindOfLeaveController extends BaseController
{
    /**
     * @param KindOfLeaveService $kindOfLeaveService
     */
    public function __construct(KindOfLeaveService $kindOfLeaveService)
    {
        $this->service = $kindOfLeaveService;
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
