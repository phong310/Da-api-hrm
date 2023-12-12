<?php

namespace App\Repositories;

use App\Models\Master\KindOfLeave;
use App\Repositories\Interfaces\KindOfLeaveInterface;

class KindOfLeaveRepository implements KindOfLeaveInterface
{
    /**
     * @var KindOfLeave
     */
    protected $kindOfLeave;

    /**
     * @param KindOfLeave $kindOfLeave
     */
    public function __construct(KindOfLeave $kindOfLeave)
    {
        $this->kindOfLeave = $kindOfLeave;
    }

    public function store($data)
    {
        return $this->kindOfLeave::query()->create($data);
    }

    public function show($id)
    {
        return $this->kindOfLeave::query()->where(['id' => $id])->first();
    }
}
