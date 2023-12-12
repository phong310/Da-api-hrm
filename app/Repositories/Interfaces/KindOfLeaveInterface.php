<?php

namespace App\Repositories\Interfaces;

interface KindOfLeaveInterface extends BaseInterface
{
    public function store($data);

    public function show($id);
}
