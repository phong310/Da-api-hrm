<?php

namespace App\Repositories\Interfaces;

interface EmployeeInterface extends BaseInterface
{
    /**
     * @param $id
     * @return mixed
     */
    public function show($id);

    /**
     * @param $data
     * @return mixed
     */
    public function store($data);

    /**
     * @param $permission
     * @return mixed
     */
    public function getEmployeesHasPermission($permission);

    /**
     * @return mixed
     */
    public function getEmployeesHasTimesheetInMonth($perPage, $employee_name);

    /**
     * @param $date
     * @param $perPage
     * @param $employee_name
     * @return mixed
     */
    public function getEmployeesHasTimesheetLogInMonth($date, $perPage, $employee_name);
}
