<?php

namespace App\Repositories\Interfaces;

interface BranchInterface extends BaseInterface
{
    public function getArrayByCompany($company_id);
}
