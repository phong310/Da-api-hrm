<?php

namespace App\Repositories\Interfaces\Forms;

interface OvertimeInterface extends BaseFormInterface
{
    /**
     * @param $id
     * @return mixed
     */
    public function show($id);

    public function store($data);
}
