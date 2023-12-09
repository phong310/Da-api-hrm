<?php

namespace App\Repositories\Interfaces\Forms;

interface CompensatoryLeaveInterface extends BaseFormInterface
{
    /**
     * @param $id
     * @return mixed
     */
    public function show($id);

    /**
     * @param $id
     * @return mixed
     */
    public function showByEmployee($id);

    /**
     * @param $data
     * @return mixed
     */
    public function store($data);

    /**
     * @param $startTime
     * @param $endTime
     * @param $id
     * @return mixed
     */
    public function checkFormIsExistByStartEndTime($startTime, $endTime, $id);

    /**
     * @param $date
     * @param $id
     * @return mixed
     */
    public function queryFormByDateExceptId($date, $id);

    public function getCompensatoryLeave($id, $companyId);
}
