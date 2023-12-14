<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Api\v1\BaseController;
use App\Http\Requests\User\StoreLeaveFormRequest;
use App\Http\Requests\User\StoreOrUpdateCompensatoryLeaveRequest;
use App\Http\Requests\User\UpdateLeaveFormRequest;
use App\Http\Services\Notifications\ExpoPushNotificationService;
use App\Http\Services\v1\Admin\ModelHasApproversService;
use App\Http\Services\v1\User\CompensatoryLeaveService;
use App\Http\Services\v1\User\NotificationService;
use App\Models\Notification;
use App\Models\TokenFcmDevices;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompensatoryLeaveController extends BaseController
{
    protected $expoService;
    /**
     * @var ModelHasApproversService
     */
    private $modelHasApproversService;
    /**
     * @var NotificationService
     */
    private $notificationService;

    /**
     * @param CompensatoryLeaveService $compensatoryLeaveService
     */
    public function __construct(
        CompensatoryLeaveService $compensatoryLeaveService,
    ) {
        $this->service = $compensatoryLeaveService;
        $this->modelHasApproversService = new ModelHasApproversService();
        $this->notificationService = new NotificationService();
    }

    /**
     * @param StoreLeaveFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreOrUpdateCompensatoryLeaveRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();
            $compensatoryLeave = $this->service->store($request);
            $approvers = $this->modelHasApproversService->store($request, $compensatoryLeave);
            foreach ($approvers as $approver) {
                $data = [
                    'model_id' => $approver['model_id'],
                    'model_type' => Notification::MODEL_TYPE['COMPENSATORY_LEAVE'],
                    'type' => Notification::TYPE['CREAT'],
                    'content' => 'CREAT',
                ];
                $this->notificationService->store($data, $approver);
            }

            DB::commit();

            return response()->json([
                'message' => __('message.created_success'),
                'data' => $compensatoryLeave,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return $this->service->errorResponse();
        }
    }

    /**
     * @param UpdateLeaveFormRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(StoreOrUpdateCompensatoryLeaveRequest $request, $id): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            $compensatoryLeave = $this->service->update($request, $id);
            $oldIdsApprovers = Arr::pluck($compensatoryLeave->approvers, 'approve_employee_id');
            $approvers = $this->modelHasApproversService->update($request, $compensatoryLeave);

            $this->notificationService->updateApprover($approvers, $oldIdsApprovers, Notification::MODEL_TYPE['COMPENSATORY_LEAVE']);

            DB::commit();

            return response()->json([
                'message' => __('message.update_success'),
                'data' => $compensatoryLeave,
                'model' => $approvers,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return $this->service->errorResponse();
        }
    }

    public function cancel($id)
    {
        return $this->service->cancel($id);
    }

    public function totalTimeOff(Request $request)
    {
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        return $this->service->totalTimeOff($startTime, $endTime);
    }
}
