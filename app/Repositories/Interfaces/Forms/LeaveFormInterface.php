<?php

namespace App\Repositories\Interfaces\Forms;

interface LeaveFormInterface extends BaseFormInterface
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

    public function queryFormHasProcessing($employee_id);

    public function getLeaveFormExit($id, $companyId);
}
