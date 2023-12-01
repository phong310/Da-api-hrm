<?php

namespace App\Repositories\Interfaces;

interface DepartmentInterface extends BaseInterface
{
    public function getArrayByCompany($company_id);
}
