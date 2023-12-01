<?php

namespace App\Repositories;

use App\Models\Master\Branch;
use App\Repositories\Interfaces\BranchInterface;

class BranchRepository implements BranchInterface
{
    protected $branch;

    public function __construct(Branch $branch)
    {
        $this->branch = $branch;
    }

    public function getArrayByCompany($company_id)
    {
        return $this->branch::query()->where('company_id', $company_id)->pluck('name')->toArray();
    }
}
