<?php

namespace App\Repositories\Interfaces\LaborContract;

use App\Repositories\Interfaces\BaseInterface;

interface LaborContractHasAllowanceInterface extends BaseInterface
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
}
