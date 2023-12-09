<?php

namespace App\Repositories\Interfaces;

interface CompensatoryWorkingDayInterface extends BaseInterface
{
    /**
     * @param $companyId
     * @param $date
     * @return mixed
     */
    public function getAnnualByDate($companyId, $date);

    /**
     * @param $companyId
     * @param $date
     * @return mixed
     */
    public function getSingleUseByDate($companyId, $date);

    /**
     * @param $companyId
     * @param $date
     * @return mixed
     */
    public function checkCompensatoryWorkingDayByDate($companyId, $date);

    /**
     * @param $id
     * @return mixed
     */
    public function show($id);

    /**
     * @param $data
     * @return mixed
     */
    public function create($data);
}
