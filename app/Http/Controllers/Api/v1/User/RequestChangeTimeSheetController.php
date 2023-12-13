<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Api\v1\BaseController;
use App\Http\Requests\User\StoreOrUpdateRequestChangeTimesheetRequest;
use App\Http\Services\v1\Admin\ModelHasApproversService;
use App\Http\Services\v1\User\NotificationService;
use App\Http\Services\v1\User\RequestChangeTimesheetService;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RequestChangeTimeSheetController extends BaseController
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        RequestChangeTimesheetService $requetsChangeTimeSheetService,
    ) {
        $this->service = $requetsChangeTimeSheetService;
        $this->modelHasApproversService = new ModelHasApproversService();
        $this->notificationService = new NotificationService();
    }

    /**
     * @param StoreOrUpdateRequestChangeTimesheetRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreOrUpdateRequestChangeTimesheetRequest $request)
    {
        try {
            DB::beginTransaction();
            $requetsChangeTimeSheet = $this->service->store($request);
            $approvers = $this->modelHasApproversService->store($request, $requetsChangeTimeSheet);
            foreach ($approvers as $approver) {
                $data = [
                    'model_id' => $approver['model_id'],
                    'model_type' => Notification::MODEL_TYPE['REQUEST_CHANGE_TIMESHEET'],
                    'type' => Notification::TYPE['CREAT'],
                    'content' => 'CREAT',
                ];
                $this->notificationService->store($data, $approver);
            }


            DB::commit();

            return response()->json([
                'message' => __('message.created_success'),
                'data' => $requetsChangeTimeSheet,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return $this->service->errorResponse();
        }
    }

    /**
     * @param StoreOrUpdateRequestChangeTimesheetRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(StoreOrUpdateRequestChangeTimesheetRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $requetsChangeTimeSheet = $this->service->update($request, $id);
            $oldIdsApprovers = Arr::pluck($requetsChangeTimeSheet->approvers, 'approve_employee_id');
            $approvers = $this->modelHasApproversService->update($request, $requetsChangeTimeSheet);

            $this->notificationService->updateApprover($approvers, $oldIdsApprovers, Notification::MODEL_TYPE['REQUEST_CHANGE_TIMESHEET']);

            DB::commit();

            return response()->json([
                'message' => __('message.update_success'),
                'data' => $requetsChangeTimeSheet,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return $this->service->errorResponse();
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function getByTimesheetId(Request $request, $id)
    {
        return $this->service->getByTimesheetId($id);
    }

    public function cancel($id)
    {
        return $this->service->cancel($id);
    }

    public function totalRequestChangeTime(Request $request)
    {
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        return $this->service->totalRequestChangeTime($startTime, $endTime);
    }
}
