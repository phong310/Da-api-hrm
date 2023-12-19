<?php

namespace App\Repositories\Interfaces\LaborContract;

use App\Repositories\Interfaces\BaseInterface;

interface AllowanceInterface extends BaseInterface
{
    /**
     * @param $id
     * @return mixed
     */
    public function show($id);

    public function store($data);
}
