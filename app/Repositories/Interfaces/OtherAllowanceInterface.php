<?php

namespace App\Repositories\Interfaces;

use App\Repositories\Interfaces\BaseInterface;

interface OtherAllowanceInterface extends BaseInterface
{
    /**
     * @param $id
     * @return mixed
     */

    public function show($Id);

    public function store($newData);

    public function update($id, $data);
}
