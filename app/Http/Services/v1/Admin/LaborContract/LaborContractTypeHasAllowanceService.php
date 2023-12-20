<?php

namespace App\Http\Services\v1\Admin\LaborContract;

use App\Http\Services\v1\BaseService;
use App\Models\LaborContract\LaborContractTypeHasAllowance;
use App\Repositories\Interfaces\LaborContract\LaborContractTypeHasAllowanceInterface;
use Illuminate\Support\Arr;

class LaborContractTypeHasAllowanceService extends BaseService
{
    /**
     * @var LaborContractTypeHasAllowanceInterface
     */
    protected $laborContractTypeHasAllowance;

    public function __construct(LaborContractTypeHasAllowanceInterface $laborContractTypeHasAllowance)
    {
        $this->laborContractTypeHasAllowance = $laborContractTypeHasAllowance;

        parent::__construct();
    }

    public function setModel()
    {
        $this->model = new LaborContractTypeHasAllowance();
    }

    public function storeArray($allowances, $laborContractTypeId)
    {
        foreach ($allowances as $allowanceId) {
            $this->laborContractTypeHasAllowance->store([
                'allowance_id' => $allowanceId,
                'labor_contract_type_id' => $laborContractTypeId
            ]);
        }
    }

    public function updateArray($allowances, $laborContractTypeId)
    {
        $oldData = $this->model::query()->where(['labor_contract_type_id' => $laborContractTypeId])->get();
        $oldIds = Arr::pluck($oldData, 'allowance_id');

        foreach ($allowances as $allowance) {
            if (!in_array($allowance, $oldIds)) {
                $this->laborContractTypeHasAllowance->store([
                    'allowance_id' => $allowance,
                    'labor_contract_type_id' => $laborContractTypeId
                ]);
            }
        }

        foreach ($oldData as $d) {
            if (!in_array($d->allowance_id, $allowances)) {
                $d->delete();
            }
        }
    }
}
