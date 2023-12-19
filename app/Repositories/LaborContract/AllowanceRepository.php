<?php

namespace App\Repositories\LaborContract;

use App\Models\LaborContract\Allowance;
use App\Repositories\Interfaces\LaborContract\AllowanceInterface;

class AllowanceRepository implements AllowanceInterface
{

    /**
     * @var Allowance
     */
    protected $allowance;

    public function __construct(Allowance $allowance)
    {
        $this->allowance = $allowance;
    }

    public function show($id)
    {
        return $this->allowance::query()->where(['id' => $id])->first();
    }

    public function store($data)
    {
        return $this->allowance::query()->create($data);
    }
}
