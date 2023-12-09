<?php

namespace App\Http\Services\v1\User;

//use App\Events\RefetchNotification;
use App\Http\Services\v1\NotificationBaseService;
use App\Models\Notification;
//use App\Traits\NotificationTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationService extends NotificationBaseService
{
    //    use NotificationTrait;

    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new Notification();
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($data, $formApprover = null)
    {
        DB::beginTransaction();
        try {
            $receiverId = $data['receiver_id'] ?? $formApprover->approve_employee_id;
            $notification = Notification::create([
                'content' => $data['content'],
                'sender_id' => Auth::user()->employee_id,
                'receiver_id' => $receiverId,
                'type' => (int)$data['type'] ?? Notification::TYPE['CREAT'],
                'status' => Notification::STATUS['NEW'],
                'model_type' => $data['model_type'],
                'model_id' => $data['model_id'],
            ]);
            DB::commit();
            return $notification;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }
    }

    public function updateApprover($approvers, $oldIdsApprovers, $modelType)
    {
        foreach ($approvers as $approver) {
            if (!in_array($approver->approve_employee_id, $oldIdsApprovers)) {
                $data = [
                    'model_id' => $approver['model_id'],
                    'model_type' => $modelType,
                    'type' => Notification::TYPE['UPDATE'],
                    'content' => 'UPDATE',
                ];
                $this->store($data, $approver);
            }
        }
    }

    public function removeNotiOfApprovers($id, $type)
    {
        $notis = $this->model->where([['model_id', $id], ['model_type', $type]])->get();
        foreach ($notis as $noti) {
            $noti->delete();
        }
    }
}
