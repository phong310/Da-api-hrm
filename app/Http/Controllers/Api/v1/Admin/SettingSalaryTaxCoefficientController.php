<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\StoreOrUpdateSettingSalaryTaxCoefficient;
use App\Http\Services\v1\Admin\SettingSalaryTaxCoefficientService;

class SettingSalaryTaxCoefficientController extends Controller
{
    private $service;
    public function __construct(SettingSalaryTaxCoefficientService $settingSalaryTaxCoefficientService)
    {
        $this->service = $settingSalaryTaxCoefficientService;
    }

    public function showSettingCoefficientByCompany()
    {
        return $this->service->showSettingCoefficientByCompany();
    }

    public function update(StoreOrUpdateSettingSalaryTaxCoefficient $request)
    {
        return $this->service->update($request);
    }
}
