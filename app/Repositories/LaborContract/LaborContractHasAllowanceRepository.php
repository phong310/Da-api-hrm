<?php

namespace App\Repositories\LaborContract;

use App\Models\LaborContract\LaborContract;
use App\Models\LaborContract\LaborContractHasAllowance;
use App\Repositories\Interfaces\LaborContract\LaborContractHasAllowanceInterface;
use App\Repositories\Interfaces\LaborContract\LaborContractInterface;

class LaborContractHasAllowanceRepository implements LaborContractHasAllowanceInterface
{


    /**
     * @var LaborContractHasAllowance
     */
    protected $laborContractHasAllowance;

    public function __construct(LaborContractHasAllowance $laborContractHasAllowance)
    {
        $this->laborContractHasAllowance = $laborContractHasAllowance;
    }

    public function show($id)
    {
        return $this->laborContractHasAllowance::query()->where(['id' => $id])->first();
    }

    public function store($data)
    {
        return $this->laborContractHasAllowance::query()->create($data);
    }
}
