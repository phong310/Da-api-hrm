<?php

namespace App\Repositories\Interfaces\Forms;

use App\Repositories\Interfaces\BaseInterface;

interface BaseFormInterface extends BaseInterface
{
    public function showByDate($date, $employee_id);

    public function checkIsApprover($formId);

    public function queryFilter($query, $date, $screenKey);
}
