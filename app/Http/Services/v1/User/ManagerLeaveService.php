<?php

namespace App\Http\Services\v1\User;

use App\Http\Controllers\Api\v1\User\ManagerController;
use App\Http\Services\Notifications\ExpoPushNotificationService;
use App\Http\Services\v1\Admin\CompanyService;
use App\Http\Services\v1\Admin\LeaveFormHasTimesheetService;
use App\Http\Services\v1\Admin\ModelHasApproversService;
use App\Http\Services\v1\Admin\TimeSheetService;
use App\Models\Form\LeaveForm;
use App\Models\Form\ModelHasApprovers;
use App\Models\Form\NumberOfDaysOff;
use App\Models\Notification;
use App\Models\TimeSheet\TimeSheet;
use App\Models\TokenFcmDevices;
use App\Repositories\Interfaces\EmployeeInterface;
use App\Repositories\Interfaces\Forms\LeaveFormInterface;
use App\Repositories\Interfaces\WorkingDayInterface;
use App\Traits\FormSetting;
use App\Transformers\ManagerTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class ManagerLeaveService extends UserBaseService
{
    use FormSetting;
    /**
     * @var ModelHasApproversService
     */
    protected $modelHasApproversService;
    /**
     * @var LeaveFormHasTimesheetService
     */
    protected $leaveFormHasTimesheetService;
    /**
     * @var TimeSheetService
     */
    protected $timesheetService;
    /**
     * @var CompanyService
     */
    protected $companyService;
    /**
     * @var WorkingDayInterface
     */
    protected $workingDay;
    /**
     * @var NotificationService
     */
    protected $notificationService;
    /**
     * @var NumberOfDaysOffService
     */
    protected $numberOfDaysOffService;
    /**
     * @var EmployeeInterface
     */
    protected $employee;
    /**
     * @var LeaveFormInterface
     */
    protected $leaveForm;

    protected $expoService;

    /**
     * @param ModelHasApproversService $modelHasApproversService
     * @param LeaveFormHasTimesheetService $leaveFormHasTimesheetService
     * @param TimeSheetService $timesheetService
     * @param CompanyService $companyService
     * @param NotificationService $notificationService
     * @param WorkingDayInterface $workingDay
     * @param NumberOfDaysOffService $numberOfDaysOffService
     * @param EmployeeInterface $employee
     * @param LeaveFormInterface $leaveForm
     */
    public function __construct(
        ModelHasApproversService $modelHasApproversService,
        // LeaveFormHasTimesheetService $leaveFormHasTimesheetService,
        // TimeSheetService $timesheetService,
        CompanyService $companyService,
        NotificationService $notificationService,
        WorkingDayInterface $workingDay,
        NumberOfDaysOffService $numberOfDaysOffService,
        EmployeeInterface $employee,
        LeaveFormInterface $leaveForm,
    ) {
        $this->modelHasApproversService = $modelHasApproversService;
        // $this->leaveFormHasTimesheetService = $leaveFormHasTimesheetService;
        // $this->timesheetService = $timesheetService;
        $this->companyService = $companyService;
        $this->workingDay = $workingDay;
        $this->notificationService = $notificationService;
        $this->numberOfDaysOffService = $numberOfDaysOffService;
        $this->employee = $employee;
        $this->leaveForm = $leaveForm;

        parent::__construct();
    }

    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new LeaveForm();
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
        $cardNumber = $this->request->get('card_number');
        $employeeName = $this->request->get('employee_name');
        $kindLeaveId = $this->request->get('kind_of_leave');
        $status = $this->request->get('status');
        $isSalary = $this->request->get('is_salary');
        $startTime = $this->request->get('start_time');
        $endTime = $this->request->get('end_time');
        $statusModelHasApprove = $this->request->get('status_model_has_approve');

        if (!is_null($cardNumber)) {
            $this->query->whereHas('employee', function ($query) use ($cardNumber) {
                $query->where('card_number', $cardNumber);
            })->orderBy('created_at', 'DESC');
        }
        if (!is_null($employeeName)) {
            $this->query->whereHas('employee', function ($q) use ($employeeName) {
                $q->whereHas('personalInformation', function ($q) use ($employeeName) {
                    $q->whereRaw(
                        "TRIM(CONCAT(first_name, ' ', last_name)) like '%{$employeeName}%'"
                    );
                });
            });
        }
        if (!is_null($kindLeaveId)) {
            $this->query->where('kind_leave_id', $kindLeaveId);
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
        if (!is_null($isSalary)) {
            $this->query->where('is_salary', intval($isSalary));
        }

        if (!is_null($startTime)) {
            $this->query->whereDate('start_time', $startTime);
        }

        if (!is_null($endTime)) {
            $this->query->whereDate('end_time', $endTime);
        }

        $month = $this->request->month;
        $screenKey = $this->request->key_screen;
        $this->query = $this->leaveForm->queryFilter($this->query, $month, $screenKey);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleForm(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        if (!Auth::user()->hasPermissionTo('leave-form.manage') || !$this->leaveForm->checkIsApprover($id)) {
            return response()->json([
                'message' => __('message.not_permission'),
            ], 403);
        }

        $form = LeaveForm::find($id);

        if (!$form) {
            return response()->json([
                'message' => __('message.not_found'),
            ], 404);
        }

        if ($form->status === LeaveForm::STATUS['CANCEL']) {
            return response()->json([
                'message' => __('message.is_form_processed'),
            ], 406);
        }

        $action = $request->action;

        $dataNoti = [
            'model_id' => $form->id,
            'model_type' => Notification::MODEL_TYPE['LEAVE'],
            'type' => Notification::TYPE['REJECT'],
            'content' => 'REJECT',
            'receiver_id' => $form['employee_id'],
        ];

        $statusOfApprover = LeaveForm::STATUS['REJECTED'];

        switch ($action) {
            case ManagerController::ACTION['ACCEPT']:
                $statusOfApprover = LeaveForm::STATUS['APPROVED'];

                $dataNoti['type'] = Notification::TYPE['ACCEPT'];
                $dataNoti['content'] = 'ACCEPT';
                break;
            case ManagerController::ACTION['REJECT-AFTER-ACCEPT'] && $form->status == LeaveForm::STATUS['APPROVED']:
                $dataNoti['content'] = 'REJECT AFTER ACCEPT';
                $form->status = LeaveForm::STATUS['REJECTED'];
                break;
            case ManagerController::ACTION['REJECT']:
                $form->status = LeaveForm::STATUS['REJECTED'];
                break;
            default:
                return response()->json([
                    'message' => 'Invalid action',
                ], 400);
        }

        $form->save();

        $dataNoti = $this->notificationService->store($dataNoti);
        $dataPushExpo['notification_id'] = $dataNoti['id'];

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
                    $form->status = LeaveForm::STATUS['APPROVED'];
                    if ($form->is_salary == LeaveForm::PAID_LEAVE['YES']) {
                        $date = $this->parseDate($form->start_time);
                        $numberDaysOff = $this->numberOfDaysOffService->store(
                            $form->employee_id,
                            $date,
                            $request->number_leave_day,
                            NumberOfDaysOff::TYPE['LEAVE_FROM']
                        );
                        $form->number_of_days_off_id = $numberDaysOff->id;
                    }

                    $this->leaveFormHasTimesheetService->store($form);
                }
                break;
            case ManagerController::ACTION['REJECT-AFTER-ACCEPT']:
                $this->updateTimesheetsBelongsToLeaveFormWhenReject($form);

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
    public function updateTimesheetsBelongsToLeaveFormWhenReject($leaveForm)
    {
        $leaveFormHasTimesheets = $leaveForm->leaveFormHasTimesheets;

        if (count($leaveFormHasTimesheets) <= 0) {
            return;
        }

        $numberOfDayOff = NumberOfDaysOff::where('id', $leaveForm->number_of_days_off_id)->first();
        $leaveForm->update(['number_of_days_off_id' => null]);

        if ($numberOfDayOff) {
            $numberOfDayOff->forceDelete();
        }
        $companyId = Auth::user()->company_id;

        $setting = $this->companyService->getSettingOfCompany($companyId);

        foreach ($leaveFormHasTimesheets as $l) {
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
                    count($timesheet->leaveFormHasTimesheets) == 0 &&
                    !$timesheet->compensatoryLeaveHasTimesheet
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
     * @return mixed
     */
    public function checkMySelf()
    {
        $employeeId = Auth::user()->employee_id;
        if (Auth::user()->hasPermissionTo('leave-form.manage')) {
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
