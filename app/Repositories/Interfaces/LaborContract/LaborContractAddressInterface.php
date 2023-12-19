<?php

namespace App\Repositories\Interfaces\LaborContract;

use App\Repositories\Interfaces\BaseInterface;

interface LaborContractAddressInterface extends BaseInterface
{
    /**
     * @param $id
     * @return mixed
     */
    public function show($id);

    /**
     * @param $data
     * @return mixed
     */
    public function store($data);

    /**
     * @param $data
     * @param $id
     * @return mixed
     */
    public function update($data, $id);
}
