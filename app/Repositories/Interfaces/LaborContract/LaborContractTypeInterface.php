<?php

namespace App\Repositories\Interfaces\LaborContract;

use App\Repositories\Interfaces\BaseInterface;

interface LaborContractTypeInterface extends BaseInterface
{
    /**
     * @param $id
     * @return mixed
     */
    public function show($id);

    public function store($data);

    public function update($data, $id);

    public function checkApplyHoliday($companyId, $employeeId);
}
