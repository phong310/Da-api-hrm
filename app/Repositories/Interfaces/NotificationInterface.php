<?php

namespace App\Repositories\Interfaces;

interface NotificationInterface extends BaseInterface
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
}
