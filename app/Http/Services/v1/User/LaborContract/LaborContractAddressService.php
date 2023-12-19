<?php

namespace App\Http\Services\v1\User\LaborContract;

use App\Http\Services\v1\BaseService;
use App\Models\LaborContract\LaborContractAddress;
use App\Repositories\Interfaces\LaborContract\LaborContractAddressInterface;

class LaborContractAddressService extends BaseService
{
    /**
     * @var LaborContractAddressInterface
     */
    protected $laborContractAddress;

    public function __construct(LaborContractAddressInterface $laborContractAddress)
    {
        $this->laborContractAddress = $laborContractAddress;

        parent::__construct();
    }

    public function setModel()
    {
        $this->model = new LaborContractAddress();
    }

    public function store($addresses, $laborContractId)
    {
        foreach ($addresses as $address) {
            $this->laborContractAddress->store(array_merge($address, ['labor_contract_id' => $laborContractId]));
        }
    }

    public function update($addresses)
    {
        foreach ($addresses as $address) {
            $this->laborContractAddress->update($address, $address['id']);
        }
    }
}
