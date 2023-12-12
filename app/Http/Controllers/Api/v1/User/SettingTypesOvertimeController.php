<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Api\v1\BaseController;
use App\Http\Services\v1\Admin\SettingTypeOvertimeService;

class SettingTypesOvertimeController extends BaseController
{
    /**
     * @var SettingTypeOvertimeService
     */
    protected $settingTypesOvertimeService;

    public function __construct(SettingTypeOvertimeService $settingTypesOvertimeService)
    {
        $this->service = $settingTypesOvertimeService;
    }
}
