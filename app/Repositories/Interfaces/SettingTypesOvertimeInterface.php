<?php

namespace App\Repositories\Interfaces;

interface SettingTypesOvertimeInterface extends BaseInterface
{
    /**
     * @param $data
     * @return mixed
     */
    public function store($data);

    /**
     * @param $id
     * @return mixed
     */
    public function show($id);

    /**
     * @param $companyId
     * @param $type
     * @return mixed
     */
    public function showByType($companyId, $type);

    /**
     * @param $data
     * @param $id
     * @return mixed
     */
    public function update($data, $id);

    /**
     * @param $date
     * @return mixed
     */
    public function showByDate($companyId, $date);
}
