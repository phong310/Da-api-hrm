<?php

namespace App\Repositories\LaborContract;

use App\Models\LaborContract\LaborContract;
use App\Models\LaborContract\LaborContractAddress;
use App\Repositories\Interfaces\LaborContract\LaborContractAddressInterface;
use App\Repositories\Interfaces\LaborContract\LaborContractInterface;

class LaborContractAddressRepository implements LaborContractAddressInterface
{
    /**
     * @var LaborContractAddress
     */
    protected $laborContractAddress;

    public function __construct(LaborContractAddress $laborContractAddress)
    {
        $this->laborContractAddress = $laborContractAddress;
    }

    public function show($id)
    {
        return $this->laborContractAddress::query()->where(['id' => $id])->first();
    }

    public function store($data)
    {
        return $this->laborContractAddress::query()->create($data);
    }

    public function updateOrCreate($dataCompare, $data)
    {
        return $this->laborContractAddress::query()->updateOrCreate($dataCompare, $data);
    }

    public function update($data, $id)
    {
        $laborContractAddress = $this->show($id);

        if (!$laborContractAddress) {
            return null;
        }

        $laborContractAddress->fill($data);
        $laborContractAddress->save();

        return $laborContractAddress;
    }
}
