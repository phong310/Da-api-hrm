<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Api\v1\BaseController;
use App\Http\Requests\User\StoreOvertimeFormRequest;
use App\Http\Requests\User\UpdateOvertimeFormRequest;
use App\Http\Services\v1\Admin\ModelHasApproversService;
use App\Http\Services\v1\User\NotificationService;
use App\Http\Services\v1\User\OverTimeService;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OverTimeController extends BaseController
{
    /**
     * @param OverTimeService $overtimeService
     */
    protected $modelHasApproversService;

    protected $notificationService;
    
    public function __construct(
        OverTimeService $overtimeService,
    ) {
        $this->service = $overtimeService;
        $this->modelHasApproversService = new ModelHasApproversService();
        $this->notificationService = new NotificationService();
    }

    /**
     * @param StoreOvertimeFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreOvertimeFormRequest $request)
    {
        try {
            DB::beginTransaction();
            $overTimeForm = $this->service->store($request);
            $approvers = $this->modelHasApproversService->store($request, $overTimeForm);
            foreach ($approvers as $approver) {
                $data = [
                    'model_id' => $approver['model_id'],
                    'model_type' => Notification::MODEL_TYPE['OVERTIME'],
                    'type' => Notification::TYPE['CREAT'],
                    'content' => 'CREAT',
                ];

                $this->notificationService->store($data, $approver);
            }

            DB::commit();

            return response()->json([
                'message' => __('message.created_success'),
                'data' => $overTimeForm,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());

            return $this->service->errorResponse();
        }
    }

    /**
     * @param UpdateOvertimeFormRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateOvertimeFormRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $overtimeForm = $this->service->update($request, $id);
            $oldIdsApprovers = Arr::pluck($overtimeForm->approvers, 'approve_employee_id');
            $approvers = $this->modelHasApproversService->update($request, $overtimeForm);

            $this->notificationService->updateApprover($approvers, $oldIdsApprovers, Notification::MODEL_TYPE['OVERTIME']);
            DB::commit();
            return response()->json([
                'message' => __('message.update_success'),
                'data' => $overtimeForm,
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

    public function totalOverTime(Request $request)
    {
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        return $this->service->totalOverTime($startTime, $endTime);
    }
}
