<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Api\v1\BaseController;
use App\Http\Requests\Admin\StoreOrUpdateSettingTypeOvertime;
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

    public function store(StoreOrUpdateSettingTypeOvertime $request)
    {
        return $this->service->store($request);
    }

    public function update(StoreOrUpdateSettingTypeOvertime $request)
    {
        return $this->service->update($request);
    }

    public function showByType($type)
    {
        return $this->service->showByType($type);
    }
}
