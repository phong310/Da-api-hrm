<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Services\v1\Admin\UserService;
use Illuminate\Http\Request;

class UserController extends BaseMasterController
{
    /**
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->service = $userService;
    }

    public function resetEmployeePassword(Request $request)
    {
        return $this->service->resetEmployeePassword($request);
    }
}
