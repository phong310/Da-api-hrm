<?php

namespace App\Repositories\Interfaces;

interface TitleInterface extends BaseInterface
{
    public function getByCompanyId($company_id);
    public function getArrayByCompany($company_id);
}
