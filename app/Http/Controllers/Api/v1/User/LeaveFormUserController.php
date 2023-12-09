<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Api\v1\BaseController;
use App\Http\Requests\User\StoreLeaveFormRequest;
use App\Http\Requests\User\UpdateLeaveFormRequest;
use App\Http\Services\Notifications\ExpoPushNotificationService;
use App\Http\Services\v1\Admin\ModelHasApproversService;
use App\Http\Services\v1\User\NumberOfDaysOffService;
use App\Http\Services\v1\User\LeaveFormService;
use App\Http\Services\v1\User\NotificationService;
use App\Models\Form\LeaveForm;
use App\Models\Notification;
use App\Models\TokenFcmDevices;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeaveFormUserController extends BaseController
{
    protected $expoService;
    /**
     * @param LeaveFormService $leaveFormService
     */

    protected $numberOfDaysOff;
    /**
     * @param NumberOfDaysOffService $leaveFormService
     */
    public function __construct(
        LeaveFormService $leaveFormService,
        NumberOfDaysOffService $numberOfDaysOffService
    ) {
        $this->numberOfDaysOff = $numberOfDaysOffService;
        $this->service = $leaveFormService;
        $this->modelHasApproversService = new ModelHasApproversService();
        $this->notificationService = new NotificationService();
    }

    /**
     * @param StoreLeaveFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreLeaveFormRequest $request)
    {
        $employee_id = Auth::user()->employee_id;
        $getDayOff = $this->numberOfDaysOff->getNumberOfDaysOffOfEmployee($employee_id);
        $numDayOff = $getDayOff['annual_leave'] - $getDayOff['leave_form'];
        $total = $this->service->totalTimeOff($request->start_time, $request->end_time);
        $leaveFormProcess = $this->service->calculateLeaveFormProcess();
        $isSalary = $request->is_salary;
        try {
            DB::beginTransaction();
            if ($isSalary === LeaveForm::PAID_LEAVE['YES']) {
                if ($numDayOff - ($leaveFormProcess + $total) < LeaveForm::KEY_SCREEN['AWAITING_CONFIRM']) {
                    return response()->json([
                        'message' => __('message.not_create_leave_form'),
                    ], 403);
                }
            }
            $leaveForm = $this->service->store($request);
            $approvers = $this->modelHasApproversService->store($request, $leaveForm);
            foreach ($approvers as $approver) {
                $data = [
                    'model_id' => $approver['model_id'],
                    'model_type' => Notification::MODEL_TYPE['LEAVE'],
                    'type' => Notification::TYPE['CREAT'],
                    'content' => 'CREAT',
                ];
                $this->notificationService->store($data, $approver);
            }
            DB::commit();
            return response()->json([
                'message' => __('message.created_success'),
                'data' => $leaveForm,
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
    public function update(UpdateLeaveFormRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $leaveForm = $this->service->update($request, $id);
            $oldIdsApprovers = Arr::pluck($leaveForm->approvers, 'approve_employee_id');
            $approvers = $this->modelHasApproversService->update($request, $leaveForm);
            $this->notificationService->updateApprover($approvers, $oldIdsApprovers, Notification::MODEL_TYPE['LEAVE']);

            DB::commit();

            return response()->json([
                'message' => __('message.update_success'),
                'data' => $leaveForm,
                'model' => $approvers,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return $this->service->errorResponse();
        }
    }

    // public function getListLeaveAppInformation(Request $request)
    // {
    //     return $this->service->getListLeaveAppInformation($request);
    // }


    public function cancel($id)
    {
        return $this->service->cancel($id);
    }

    // public function totalTimeOff(Request $request)
    // {
    //     $startTime = $request->start_time;
    //     $endTime = $request->end_time;
    //     return $this->service->totalTimeOff($startTime, $endTime);
    // }
}
