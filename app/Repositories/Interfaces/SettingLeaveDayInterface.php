<?php

namespace App\Repositories\Interfaces;

interface SettingLeaveDayInterface extends BaseInterface
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
     * @return mixed
     */
    public function update($data, $id);

    /**
     * @param $companyId
     * @param $positionId
     * @param $date
     * @param $type
     * @return mixed
     */
    public function checkPositionHasLeaveDay($companyId, $positionId, $date, $type);

    /**
     * @param $companyId
     * @param $departmentId
     * @param $date
     * @param $type
     * @return mixed
     */
    public function checkDepartmentHasLeaveDay($companyId, $departmentId, $date, $type);
}
