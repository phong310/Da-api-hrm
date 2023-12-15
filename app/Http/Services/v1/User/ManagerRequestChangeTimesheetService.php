<?php

namespace App\Http\Services\v1\User;

use App\Http\Controllers\Api\v1\User\ManagerController;
use App\Http\Services\Notifications\ExpoPushNotificationService;
use App\Http\Services\v1\Admin\CompanyService;
use App\Http\Services\v1\Admin\ModelHasApproversService;
use App\Http\Services\v1\Admin\TimeSheetService;
use App\Models\Form\ModelHasApprovers;
use App\Models\Form\RequestChangeTimesheet;
use App\Models\Notification;
use App\Models\TimeSheet\TimeSheet;
use App\Models\TokenFcmDevices;
use App\Repositories\Interfaces\EmployeeInterface;
use App\Repositories\Interfaces\Forms\RequestChangeTimesheetInterface;
use App\Repositories\Interfaces\TimeSheetLogInterface;
use App\Repositories\Interfaces\WorkingDayInterface;
use App\Traits\FormSetting;
use App\Transformers\ManagerTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class ManagerRequestChangeTimesheetService extends UserBaseService
{
    use FormSetting;
    /**
     * @var TimeSheetService
     */
    protected $timesheetService;
    /**
     * @var CompanyService
     */
    protected $companyService;
    /**
     * @var ModelHasApproversService
     */
    protected $modelHasApproversService;
    /**
     * @var WorkingDayInterface
     */
    protected $workingDay;
    /**
     * @var NotificationService
     */
    protected $notificationService;
    /**
     * @var EmployeeInterface
     */
    protected $employee;
    /**
     * @var RequestChangeTimesheetInterface
     */
    private $requestChangeTimesheet;

    private $timeSheetLog;

    protected $expoService;

    /**
     * @param ModelHasApproversService $modelHasApproversService
     * @param TimeSheetService $timesheetService
     * @param CompanyService $companyService
     * @param WorkingDayInterface $workingDay
     * @param NotificationService $notificationService
     * @param EmployeeInterface $employee
     * @param RequestChangeTimesheetInterface $requestChangeTimesheet
     */
    public function __construct(
        ModelHasApproversService $modelHasApproversService,
        // TimeSheetService $timesheetService,
        CompanyService $companyService,
        WorkingDayInterface $workingDay,
        NotificationService $notificationService,
        EmployeeInterface $employee,
        RequestChangeTimesheetInterface $requestChangeTimesheet,
        TimeSheetLogInterface $timeSheetLog,
    ) {
        $this->modelHasApproversService = $modelHasApproversService;
        // $this->timesheetService = $timesheetService;
        $this->companyService = $companyService;
        $this->workingDay = $workingDay;
        $this->notificationService = $notificationService;
        $this->employee = $employee;
        $this->requestChangeTimesheet = $requestChangeTimesheet;
        $this->timeSheetLog = $timeSheetLog;

        parent::__construct();
    }

    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new RequestChangeTimesheet();
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

        if (!is_null($cardNumber)) {
            $this->query->whereHas('employee', function ($query) use ($cardNumber) {
                $query->where('card_number', $cardNumber);
            })->orderBy('created_at', 'DESC');
        }

        $employeeName = $this->request->get('employee_name');
        if ($employeeName) {
            $this->query->whereHas('employee', function ($q) use ($employeeName) {
                $q->whereHas('personalInformation', function ($q) use ($employeeName) {
                    $q->whereRaw(
                        "TRIM(CONCAT(first_name, ' ', last_name)) like '%{$employeeName}%'"
                    );
                });
            });
        }

        $status = $this->request->get('status');
        if (!is_null($status)) {
            $this->query->where('status', $status);
        }

        $statusModelHasApprove = $this->request->get('status_model_has_approve');
        if (!is_null($statusModelHasApprove)) {
            $thisApproverEmployeeId = Auth::user()->employee_id;

            $this->query->whereHas('approvers', function ($q) use ($thisApproverEmployeeId, $statusModelHasApprove) {
                $q->where(['approve_employee_id' => $thisApproverEmployeeId, 'status' => $statusModelHasApprove]);
            });
        }

        $date = $this->request->get('date');
        if (!is_null($date)) {
            $this->query->whereDate('date', $date);
        }


        $this->query->with('approvers');

        $month = $this->request->month;
        $screenKey = $this->request->key_screen;
        $this->query = $this->requestChangeTimesheet->queryFilter($this->query, $month, $screenKey);
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function handleForm(Request $request, $id): JsonResponse
    {
        if (!Auth::user()->hasPermissionTo('request-change-timesheets.manage') || !$this->requestChangeTimesheet->checkIsApprover($id)) {
            return response()->json([
                'message' => __('message.not_permission'),
            ], 400);
        }

        $form = RequestChangeTimesheet::find($id);

        if (!$form) {
            return response()->json([
                'message' => __('message.not_found')
            ], 404);
        }

        if ($form->status === RequestChangeTimesheet::STATUS['CANCEL']) {
            return response()->json([
                'message' => __('message.is_form_processed'),
            ], 406);
        }


        $action = $request->action;
        $statusOfApprover = RequestChangeTimesheet::STATUS['REJECTED'];

        $companyId = $request->user()->company_id;
        $employeeId = $request->employee_id;

        $dataNoti = [
            'model_id' => $form['id'],
            'model_type' => Notification::MODEL_TYPE['REQUEST_CHANGE_TIMESHEET'],
            'type' => Notification::TYPE['REJECT'],
            'content' => 'REJECT',
            'receiver_id' => $form['employee_id'],
        ];

    

        switch ($action) {
            case ManagerController::ACTION['ACCEPT']:
                $statusOfApprover = RequestChangeTimesheet::STATUS['APPROVED'];

                $dataNoti['type'] = Notification::TYPE['ACCEPT'];
                $dataNoti['content'] = 'ACCEPT';
                break;
            case ManagerController::ACTION['REJECT-AFTER-ACCEPT'] && $form->status == RequestChangeTimesheet::STATUS['APPROVED']:
                $dataNoti['content'] = 'REJECT AFTER ACCEPT';

                $form->status = RequestChangeTimesheet::STATUS['REJECTED'];
                $form->update(['timesheet_id' => null]);
                break;
            case ManagerController::ACTION['REJECT']:
                $form->status = RequestChangeTimesheet::STATUS['REJECTED'];
                break;
            default:
                return response()->json([
                    'message' => 'Invalid action',
                ], 400);
        }

        $form->save();
        $dataNoti = $this->notificationService->store($dataNoti);
        $dataPushExpo['notification_id'] = $dataNoti['id'];

        $approve_id = $request->user()->employee_id;
        $this->modelHasApproversService->updateStatus($id, $approve_id, get_class($form), $statusOfApprover);

        $this->afterChangeStatusForm($form, $companyId, $employeeId, $action);

        return response()->json([
            'message' => __('message.update_success'),
        ]);
    }

    /**
     * @param $form
     * @param $companyId
     * @param $employeeId
     * @param $action
     * @return void
     */
    public function afterChangeStatusForm($form, $companyId, $employeeId, $action)
    {
        $setting = $this->companyService->getSettingOfCompany($companyId);
        $workingDay = $this->workingDay->showWorkingDayByDate($companyId, $form['date']);

        switch ($action) {
            case ManagerController::ACTION['ACCEPT']:
                $isAvailableAccept = collect(Arr::pluck($form->approvers, 'status'))->every(function ($value) {
                    return $value == ModelHasApprovers::STATUS['APPROVED'];
                });
                //                $isAvailableAccept = true;
                if ($isAvailableAccept) {
                    $form->status = RequestChangeTimesheet::STATUS['APPROVED'];
                    $data = [
                        'employee_id' => $form['employee_id'],
                        'company_id' => $companyId,
                        'date' => $form['date'],
                        'start_time' => $form['check_in_time'],
                        'end_time' => $form['check_out_time'],
                    ];

                    if ($workingDay) { 
                        $timesheet = $this->timesheetService->updateOrCreateData($data, $workingDay, $setting, 'schedule', true);
                        $form->timesheet_id = $timesheet->id;
                    }
                }
                break;
            case ManagerController::ACTION['REJECT-AFTER-ACCEPT']:
                $timesheet = TimeSheet::where([
                    'date' => $form->date,
                    'employee_id' => $form->employee_id,
                    'company_id' => $companyId,
                ])->first();

                $data = [
                    'employee_id' => $employeeId,
                    'company_id' => $companyId,
                    'date' => $form['date'],
                    'start_time' => $timesheet['real_start_time'],
                    'end_time' => $timesheet['real_end_time'],
                ];

                $this->timesheetService->updateOrCreateData($data, $workingDay, $setting, 'schedule', true);
                $timesheet = $timesheet->refresh();
                if ((!$timesheet['real_start_time'] || !$timesheet['real_end_time'] || !$timesheet['real_total_time_work'])
                    && !$timesheet->overtime && count($timesheet->leaveFormHasTimesheets) == 0 && !$timesheet->compensatoryLeaveHasTimesheet
                ) {
                    $timesheet->forceDelete();
                }
                break;
            default:
                break;
        }
        $form->save();
    }

    /**
     * @return JsonResponse
     */
    public function getApprovers(): JsonResponse
    {
        return response()->json($this->employee->getEmployeesHasPermission('request-change-timesheets.manage'));
    }

    /**
     * @param $data
     * @return mixed
     */
    public function afterFind($data)
    {
        $timeSheetLogReal = $this->timeSheetLog->getTimeSheetLogOnDate($data['employee_id'], $data['date']);
        $data['timesheets_logs'] = $timeSheetLogReal;
        return $this->customDataApproval($data);
    }

    /**
     * @return mixed
     */
    public function checkMySelf()
    {
        $employeeId = Auth::user()->employee_id;
        if (!Auth::user()->hasPermissionTo('request-change-timesheets.manage')) {
            $this->query->whereNull('employee_id');
        } else {
            $this->query->whereHas('approvers', function ($q) use ($employeeId) {
                $q->where(['approve_employee_id' => $employeeId]);
            });
        }
        $this->query->with('approvers.employee.information');
    }
}
