<?php

namespace App\Repositories;

use App\Models\Master\Department;
use App\Repositories\Interfaces\DepartmentInterface;

class DepartmentRepository implements DepartmentInterface
{
    protected $department;

    public function __construct(Department $department)
    {
        $this->department = $department;
    }

    public function getArrayByCompany($company_id)
    {
        return $this->department::query()->where('company_id', $company_id)->pluck('name')->toArray();
    }
}
