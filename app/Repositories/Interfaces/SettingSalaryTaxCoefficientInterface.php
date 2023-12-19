<?php

namespace App\Repositories\Interfaces;

interface SettingSalaryTaxCoefficientInterface extends BaseInterface
{
    public function showSettingCoefficientByCompany($companyId);
    public function update($data, $id);
}
