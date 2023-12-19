<?php

namespace App\Repositories\LaborContract;

use App\Models\LaborContract\LaborContractType;
use App\Repositories\Interfaces\LaborContract\LaborContractTypeInterface;

class LaborContractTypeRepository implements LaborContractTypeInterface
{

    protected $laborContractType;

    /**
     * @param LaborContractType $laborContractType
     */
    public function __construct(LaborContractType $laborContractType)
    {
        $this->laborContractType = $laborContractType;
    }

    public function show($id)
    {
        return $this->laborContractType::query()->where(['id' => $id])->first();
    }

    public function store($data)
    {
        return $this->laborContractType::query()->create($data);
    }

    public function update($data, $id)
    {
        $laborContractType = $this->show($id);

        if (!$laborContractType) {
            return null;
        }

        $laborContractType->fill($data);
        $laborContractType->save();

        return $laborContractType;
    }

    public function checkApplyHoliday($companyId, $employeeId)
    {
        $laborContractType = LaborContractType::query()->where('company_id', $companyId)->whereHas('laborContract', function ($q) use ($employeeId) {
            $q->where('employee_id', $employeeId);
        })->first();
        if ($laborContractType) {
            if ($laborContractType->status_apply_holiday === LaborContractType::STATUS_HOLIDAY['APPLY']) {
                return true;
            }
        }
        return false;
    }
}
