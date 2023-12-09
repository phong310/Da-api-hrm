<?php

namespace App\Repositories;

use App\Models\Notification;
use App\Repositories\Interfaces\NotificationInterface;

class NotificationRepository implements NotificationInterface
{
    /**
     * @var Notification
     */
    protected $notification;

    /**
     * @param Notification $notification
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function store($data)
    {
        return $this->notification::query()->create($data);
    }

    public function show($id)
    {
        return $this->notification::query()->where(['id' => $id])->first();
    }
}
