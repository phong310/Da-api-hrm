<?php

namespace App\Http\Services\v1\User\LaborContract;

use App\Http\Services\v1\BaseService;
use App\Models\LaborContract\LaborContractHasAllowance;
use App\Models\LaborContract\LaborContractTypeHasAllowance;
use App\Repositories\Interfaces\LaborContract\LaborContractHasAllowanceInterface;
use Illuminate\Support\Arr;

class LaborContractHasAllowanceService extends BaseService
{
    /**
     * @var LaborContractHasAllowanceInterface
     */
    protected $laborContractHasAllowance;

    public function __construct(LaborContractHasAllowanceInterface $laborContractHasAllowance)
    {
        $this->laborContractHasAllowance = $laborContractHasAllowance;

        parent::__construct();
    }

    public function setModel()
    {
        $this->model = new LaborContractHasAllowance();
    }

    public function storeArray($allowances, $laborContractId)
    {
        foreach ($allowances as $allowance) {
            $this->laborContractHasAllowance->store([
                'allowance_id' => $allowance,
                'labor_contract_id' => $laborContractId
            ]);
        }
    }

    public function updateArray($allowances, $laborContractId)
    {
        $oldData = $this->model::query()->where(['labor_contract_id' => $laborContractId])->get();
        $oldIds = Arr::pluck($oldData, 'allowance_id');

        foreach ($allowances as $allowance) {
            if (!in_array($allowance, $oldIds)) {
                $this->laborContractHasAllowance->store([
                    'allowance_id' => $allowance,
                    'labor_contract_id' => $laborContractId
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
