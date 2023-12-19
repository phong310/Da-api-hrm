<?php

namespace App\Repositories;

use App\Models\Setting\SettingSalaryTaxCoefficient;
use App\Repositories\Interfaces\SettingSalaryTaxCoefficientInterface;

class SettingSalaryTaxCoefficientRepository implements SettingSalaryTaxCoefficientInterface
{
    protected $settingSalaryTaxCoefficient;

    public function __construct(SettingSalaryTaxCoefficient $settingSalaryTaxCoefficient)
    {
        $this->settingSalaryTaxCoefficient = $settingSalaryTaxCoefficient;
    }

    public function show($id)
    {
        return $this->settingSalaryTaxCoefficient::query()->where(['id' => $id])->first();
    }

    public function showSettingCoefficientByCompany($companyId)
    {
        return $this->settingSalaryTaxCoefficient::query()->where(['company_id' => $companyId])
            ->first();
    }


    public function update($data, $id)
    {
        $setting_salary_tax_coefficient = $this->show($id);

        if (!$setting_salary_tax_coefficient) {
            return null;
        }

        $setting_salary_tax_coefficient->fill($data);
        $setting_salary_tax_coefficient->save();

        return $setting_salary_tax_coefficient;
    }
}
