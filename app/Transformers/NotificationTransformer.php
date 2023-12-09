<?php

namespace App\Transformers;

use App\Models\Notification;
use League\Fractal\TransformerAbstract;

class NotificationTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Notification $notification)
    {
        $arr = explode('\\', $notification['model_type']);
        $model_type = $arr[count($arr) - 1];

        return [
            'id' => $notification->id,
            'content' => $notification->content,
            'status' => $notification->status,
            'created_at' => $notification->created_at,
            'model_id' => $notification->model_id,
            'model_type' => $model_type,
            'type' => $notification->type,
            'full_name_sender' => $notification->sender->personalInformation->full_name ?? null,
        ];
    }
}
