<?php

namespace App\Http\Services\v1\User;

use App\Http\Controllers\Api\v1\User\ManagerController;
use App\Http\Services\Notifications\ExpoPushNotificationService;
use App\Http\Services\v1\Admin\CompanyService;
use App\Http\Services\v1\Admin\CompensatoryLeaveHasTimesheetService;
use App\Http\Services\v1\Admin\LeaveFormHasTimesheetService;
use App\Http\Services\v1\Admin\ModelHasApproversService;
use App\Http\Services\v1\Admin\TimeSheetService;
use App\Models\Form\CompensatoryLeave;
use App\Models\Form\ModelHasApprovers;
use App\Models\Notification;
use App\Models\TimeSheet\TimeSheet;
use App\Models\TokenFcmDevices;
use App\Repositories\Interfaces\EmployeeInterface;
use App\Repositories\Interfaces\Forms\CompensatoryLeaveInterface;
use App\Repositories\Interfaces\WorkingDayInterface;
use App\Traits\FormSetting;
use App\Transformers\ManagerTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class ManagerCompensatoryLeaveService extends UserBaseService
{
    use FormSetting;

    /**
     * @var CompensatoryLeaveInterface
     */
    private $compensatoryLeave;
    /**
     * @var EmployeeInterface
     */
    private $employee;
    /**
     * @var NotificationService
     */
    private $notificationService;
    /**
     * @var WorkingDayInterface
     */
    private $workingDay;
    /**
     * @var CompanyService
     */
    private $companyService;
    /**
     * @var TimeSheetService
     */
    private $timesheetService;
    /**
     * @var LeaveFormHasTimesheetService
     */
    private $leaveFormHasTimesheetService;
    /**
     * @var ModelHasApproversService
     */
    private $modelHasApproversService;
    /**
     * @var CompensatoryLeaveHasTimesheetService
     */
    private $compensatoryLeaveHasTimesheetService;

    private $expoService;

    public function __construct(
        ModelHasApproversService $modelHasApproversService,
        CompensatoryLeaveHasTimesheetService $compensatoryLeaveHasTimesheetService,
        TimeSheetService $timesheetService,
        CompanyService $companyService,
        NotificationService $notificationService,
        WorkingDayInterface $workingDay,
        EmployeeInterface $employee,
        CompensatoryLeaveInterface $compensatoryLeave,
    ) {
        $this->modelHasApproversService = $modelHasApproversService;
        $this->compensatoryLeaveHasTimesheetService = $compensatoryLeaveHasTimesheetService;
        $this->timesheetService = $timesheetService;
        $this->companyService = $companyService;
        $this->workingDay = $workingDay;
        $this->notificationService = $notificationService;
        $this->employee = $employee;
        $this->compensatoryLeave = $compensatoryLeave;

        parent::__construct();
    }

    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new CompensatoryLeave();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function setTransformers($data)
    {
        $collection = $data->getCollection();
        $all_form = collect($collection)->transformWith(new ManagerTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));

        return $all_form;
    }

    public function appendFilter()
    {
        $card_number = $this->request->get('card_number');
        $employee_name = $this->request->get('employee_name');
        $kind_leave_id = $this->request->get('kind_of_leave');
        $status = $this->request->get('status');
        $startTime = $this->request->get('start_time');
        $endTime = $this->request->get('end_time');
        $statusModelHasApprove = $this->request->get('status_model_has_approve');

        if (!is_null($card_number)) {
            $this->query->whereHas('employee', function ($query) use ($card_number) {
                $query->where('card_number', $card_number);
            })->orderBy('created_at', 'DESC');
        }
        if (!is_null($employee_name)) {
            $this->query->whereHas('employee', function ($q) use ($employee_name) {
                $q->whereHas('personalInformation', function ($q) use ($employee_name) {
                    $q->whereRaw(
                        "TRIM(CONCAT(first_name, ' ', last_name)) like '%{$employee_name}%'"
                    );
                });
            });
        }
        if (!is_null($kind_leave_id)) {
            $this->query->where('kind_leave_id', $kind_leave_id);
        }
        if (!is_null($status)) {
            $this->query->where('status', $status);
        }
        if (!is_null($statusModelHasApprove)) {
            $thisApproverEmployeeId = Auth::user()->employee_id;

            $this->query->whereHas('approvers', function ($q) use ($thisApproverEmployeeId, $statusModelHasApprove) {
                $q->where(['approve_employee_id' => $thisApproverEmployeeId, 'status' => $statusModelHasApprove]);
            });
        }

        if (!is_null($startTime)) {
            $this->query->whereDate('start_time', $startTime);
        }

        if (!is_null($endTime)) {
            $this->query->whereDate('end_time', $endTime);
        }

        $month = $this->request->month;
        $screenKey = $this->request->key_screen;
        $this->query = $this->compensatoryLeave->queryFilter($this->query, $month, $screenKey);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleForm(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        // Chinh lai perm @@
        if (!Auth::user()->hasPermissionTo('compensatory_leave.manage') || !$this->compensatoryLeave->checkIsApprover($id)) {
            return response()->json([
                'message' => __('message.not_permission'),
            ], 403);
        }

        $form = CompensatoryLeave::find($id);

        if (!$form) {
            return response()->json([
                'message' => __('message.not_found'),
            ], 404);
        }

        $action = $request->action;

        $dataNoti = [
            'model_id' => $form->id,
            'model_type' => Notification::MODEL_TYPE['COMPENSATORY_LEAVE'],
            'type' => Notification::TYPE['REJECT'],
            'content' => 'REJECT',
            'receiver_id' => $form['employee_id'],
        ];

        $fullNameSender = $this->expoService->getFullNameSenderNoti();

        $statusOfApprover = CompensatoryLeave::STATUS['REJECTED'];

        switch ($action) {
            case ManagerController::ACTION['ACCEPT']:
                $statusOfApprover = CompensatoryLeave::STATUS['APPROVED'];

                $dataNoti['type'] = Notification::TYPE['ACCEPT'];
                $dataNoti['content'] = 'ACCEPT';
                break;
            case ManagerController::ACTION['REJECT-AFTER-ACCEPT'] && $form->status == CompensatoryLeave::STATUS['APPROVED']:
                $dataNoti['content'] = 'REJECT AFTER ACCEPT';
                $form->status = CompensatoryLeave::STATUS['REJECTED'];
                break;
            case ManagerController::ACTION['REJECT']:
                $form->status = CompensatoryLeave::STATUS['REJECTED'];
                break;
            default:
                return response()->json([
                    'message' => 'Invalid action',
                ], 400);
        }

        $form->save();

        $dataNoti = $this->notificationService->store($dataNoti);

        $approveId = $request->user()->employee_id;
        $this->modelHasApproversService->updateStatus($id, $approveId, get_class($form), $statusOfApprover);

        $this->afterChangeStatusForm($form, $request, $action);

        return response()->json([
            'message' => __('message.update_success'),
        ], 200);
    }

    /**
     * @param $form
     * @param $request
     * @param $action
     * @return void
     */
    public function afterChangeStatusForm($form, $request, $action)
    {
        switch ($action) {
            case ManagerController::ACTION['ACCEPT']:
                $isAvailableAccept = collect(Arr::pluck($form->approvers, 'status'))->every(function ($value) {
                    return $value == ModelHasApprovers::STATUS['APPROVED'];
                });
                //                $isAvailableAccept = true;
                if ($isAvailableAccept) {
                    $form->status = CompensatoryLeave::STATUS['APPROVED'];

                    $this->compensatoryLeaveHasTimesheetService->store($form);
                }
                break;
            case ManagerController::ACTION['REJECT-AFTER-ACCEPT']:
                $this->updateTimesheetsBelongsToCompensatoryLeaveWhenReject($form);

                break;
            default:
                break;
        }
        $form->save();
    }

    /**
     * @param $timesheet
     * @param $setting
     */
    public function updateTimeSheet($timesheet, $setting)
    {
        $companyId = $timesheet['company_id'];
        $employeeId = $timesheet['employee_id'];

        $data = [
            'employee_id' => $employeeId,
            'company_id' => $companyId,
            'date' => $timesheet['date'],
            'start_time' => $timesheet['real_start_time'] ?? $timesheet['start_time'],
            'end_time' => $timesheet['real_end_time'] ?? $timesheet['end_time'],
        ];
        $workingDay = $this->workingDay->showWorkingDayByDate($companyId, $timesheet['date']);

        $this->timesheetService->updateOrCreateData($data, $workingDay, $setting, 'schedule', true);
    }

    /**
     * @param $leaveForm
     */
    public function updateTimesheetsBelongsToCompensatoryLeaveWhenReject($compensatoryLeave)
    {
        $compensatoryLeaveHasTimeSheet = $compensatoryLeave->compensatoryLeaveHasTimeSheet;

        if (count($compensatoryLeaveHasTimeSheet) <= 0) {
            return;
        }

        $companyId = Auth::user()->company_id;

        $setting = $this->companyService->getSettingOfCompany($companyId);

        foreach ($compensatoryLeaveHasTimeSheet as $l) {
            $timesheet = TimeSheet::where('id', $l->timesheet_id)->first();

            if ($timesheet && $l) {

                $l->delete();

                $this->updateTimeSheet($timesheet, $setting);
                $timesheet = $timesheet->refresh();
                if (
                    !$timesheet['real_total_time_work'] &&
                    !$timesheet['total_time_work'] &&
                    !$timesheet['start_time'] &&
                    !$timesheet['end_time'] &&
                    !$timesheet->overtime &&
                    count($timesheet->leaveFormHasTimesheets) == 0
                ) {
                    $timesheet->forceDelete();
                }
            }
        }
    }

    /**
     * @param $data
     * @return mixed
     */
    public function afterFind($data)
    {
        return $this->customDataApproval($data);
    }

    /**
     * @return void
     */
    public function checkMySelf()
    {
        $employeeId = Auth::user()->employee_id;
        if (Auth::user()->hasPermissionTo('compensatory_leave.manage')) {
            $this->query->whereHas('approvers', function ($q) use ($employeeId) {
                $q->where(['approve_employee_id' => $employeeId]);
            });
        } else {
            $this->query->whereNull('employee_id');
        }
        $this->query->with('approvers.employee.information');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getApprovers()
    {
        return response()->json($this->employee->getEmployeesHasPermission('leave-form.manage'));
    }
}
