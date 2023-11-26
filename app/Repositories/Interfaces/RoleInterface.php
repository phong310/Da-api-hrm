<?php

namespace App\Repositories\Interfaces;

interface RoleInterface extends BaseInterface
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

    /**
     * @param $data_filter
     * @param $data
     * @return mixed
     */
    public function updateOrCreate($data_filter, $data);

    /**
     * @param $name
     * @param $companyId
     * @return mixed
     */
    public function showByName($name, $companyId);
}
