<?php

namespace App\Repositories\Interfaces\LaborContract;

use App\Repositories\Interfaces\BaseInterface;

interface LaborContractInterface extends BaseInterface
{
    /**
     * @param $id
     * @param $relations
     * @return mixed
     */
    public function show($id, $relations);

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

    /**
     * @param $employeeId
     * @return mixed
     */
    public function showActive($employeeId);

    public function showSalaryContract($employeeId, $endDate, $startDate);

    public function queryValidateUnique($company_id, $value);

    public function checkExpiringContract($status);
}
