<?php

namespace App\Repositories\LaborContract;

use App\Models\LaborContract\LaborContractTypeHasAllowance;
use App\Repositories\Interfaces\LaborContract\LaborContractTypeHasAllowanceInterface;

class LaborContractTypeHasAllowanceRepository implements LaborContractTypeHasAllowanceInterface
{

    /**
     * @var LaborContractTypeHasAllowance
     */
    protected $laborContractTypeHasAllowance;

    public function __construct(LaborContractTypeHasAllowance $laborContractTypeHasAllowance)
    {
        $this->laborContractTypeHasAllowance = $laborContractTypeHasAllowance;
    }

    public function show($id)
    {
        return $this->laborContractTypeHasAllowance::query()->where(['id' => $id])->first();
    }

    public function store($data)
    {
        return $this->laborContractTypeHasAllowance::query()->create($data);
    }
}
