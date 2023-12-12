<?php

namespace App\Repositories\Interfaces\Forms;

interface RequestChangeTimesheetInterface extends BaseFormInterface
{
    /**
     * @param $id
     * @return mixed
     */
    public function show($id);
}
