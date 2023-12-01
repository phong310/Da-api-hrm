<?php

namespace App\Repositories\Interfaces;

interface NumberOfDaysOffInterface extends BaseInterface
{
    /**
     * @param $data
     * @return mixed
     */
    public function create($data);

    /**
     * @param $id
     * @return mixed
     */
    public function show($id);

    /**
     * @param $data
     * @param $id
     * @return mixed
     */
    public function update($data, $id);
}
