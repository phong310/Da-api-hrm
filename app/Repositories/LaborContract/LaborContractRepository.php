<?php

namespace App\Repositories\LaborContract;

use App\Models\LaborContract\LaborContract;
use App\Repositories\Interfaces\LaborContract\LaborContractInterface;
use Carbon\Carbon;

class LaborContractRepository implements LaborContractInterface
{

    protected $laborContract;

    /**
     * @param LaborContract $laborContract
     */
    public function __construct(LaborContract $laborContract)
    {
        $this->laborContract = $laborContract;
    }

    public function show($id, $relations = [])
    {
        return $this->laborContract::query()->where(['id' => $id])->with($relations)->first();
    }

    public function store($data)
    {
        $employeeId = $data['employee_id'];
        $this->terminateContract($employeeId);

        $newContract = $this->laborContract::query()->create($data);

        return $newContract;
    }


    public function update($data, $id)
    {
        $laborContract = $this->show($id);

        if (!$laborContract) {
            return null;
        }

        $laborContract->fill($data);
        $laborContract->save();

        return $laborContract;
    }

    public function terminateContract($employeeId)
    {
        $contract = $this->laborContract::where('employee_id', $employeeId)
            ->where('status', LaborContract::STATUS['EXPIRTION'])
            ->first();

        if ($contract) {
            $contract->update(['status' => LaborContract::STATUS['TERMINATE']]);
        }

        return $contract;
    }


    public function getAllMySelf($employeeId)
    {
        return $this->laborContract::query()->where(['employee_id' => $employeeId])
            ->with('employee.personalInformation')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage ?? 20);
    }

    public function getDetailMySelf($employee_id, $id)
    {
        return $this->laborContract::query()->where(['employee_id' => $employee_id])
            ->where(['id' => $id])
            ->with(['employee.personalInformation', 'allowances.allowance'])
            ->first();
    }

    public function showActive($employeeId)
    {
        return $this->laborContract::query()
            ->where(['employee_id' => $employeeId])
            ->where(['status' => LaborContract::STATUS['ACTIVE']])
            ->with(['allowances.allowance'])
            ->first();
    }

    public function showSalaryContract($employeeId, $endDate, $startDate)
    {
        return $this->laborContract::query()
            ->where(['employee_id' => $employeeId])
            ->where(function ($query) use ($endDate, $startDate) {
                $query->where('status', '=', LaborContract::STATUS['ACTIVE'])
                    ->where('effective_date', '<=', $endDate)
                    ->orWhere(function ($q) use ($endDate, $startDate) {
                        $q->where('status', '=', LaborContract::STATUS['TERMINATE'])
                            ->where('termination_date', '>=', $startDate)->where('effective_date', '<=', $endDate);
                    })
                    ->orWhere(function ($q) use ($endDate, $startDate) {
                        $q->where('status', '=', LaborContract::STATUS['EXPIRTION'])
                            ->where('expire_date', '>=', $startDate)->where('effective_date', '<=', $endDate);
                    });
            })
            ->orderBy('created_at', 'desc')
            ->with(['allowances.allowance'])
            ->first();
    }

    public function queryValidateUnique($company_id, $value)
    {
        return $this->laborContract::query()
            ->where('company_id', $company_id)
            ->where('code', $value)
            ->whereIn('status', [LaborContract::STATUS['ACTIVE'], LaborContract::STATUS['EXTEND']]);
    }

    public function checkExpiringContract($status = null)
    {
        $now = Carbon::now();
        $query = laborContract::query();

        if ($status) {
            $query->where('status', $status);
        }
        if ($status === LaborContract::STATUS['ACTIVE']) {
            $query->where('expire_date', '<=', $now->addDays(LaborContract::EXPIRATION_DAY));
        }

        return $query;
    }
}
