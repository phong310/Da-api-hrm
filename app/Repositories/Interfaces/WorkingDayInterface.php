<?php

namespace App\Repositories\Interfaces;

interface WorkingDayInterface extends BaseInterface
{
    /**
     * @param $data
     * @return mixed
     */
    public function store($data);

    /**
     * @param $data
     * @return mixed
     */
    public function stores($data);

    /**
     * @param $id
     * @return mixed
     */
    public function show($id);

    /**
     * @param $companyId
     * @param $date
     * @return mixed
     */
    public function showWorkingDayByDate($companyId, $date);

    /**
     * @param $companyId
     * @param $dateTime
     * @return mixed
     */
    public function isTimeInWorkingDay($companyId, $dateTime);
}
