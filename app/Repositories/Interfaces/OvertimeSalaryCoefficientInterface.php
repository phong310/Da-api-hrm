<?php

namespace App\Repositories\Interfaces;

interface OvertimeSalaryCoefficientInterface extends BaseInterface
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
     * @param $overtime
     * @return mixed
     */
    public function storeByRangeTime($overtime);

    /**
     * @param $overtimeId
     * @return mixed
     */
    public function destroyByOvertimeId($overtimeId);
}
