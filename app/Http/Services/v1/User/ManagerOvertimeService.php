<?php

namespace App\Http\Services\v1\User;

use App\Http\Controllers\Api\v1\User\ManagerController;
use App\Http\Services\v1\Admin\ModelHasApproversService;
use App\Models\Form\ModelHasApprovers;
use App\Models\Form\OverTime;
use App\Models\Notification;
use App\Repositories\Interfaces\EmployeeInterface;
use App\Repositories\Interfaces\Forms\OvertimeInterface;
use App\Traits\FormSetting;
use App\Transformers\ManagerTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class ManagerOvertimeService extends UserBaseService
{
    use FormSetting;
    /**
     * @var ModelHasApproversService
     */
    protected $modelHasApproversService;
    /**
     * @var NotificationService
     */
    protected $notificationService;

    /**
     * @var OvertimeInterface
     */
    protected $overtime;

    /**
     * @var EmployeeInterface
     */
    protected $employee;
    /**
     * @var TimeSheetService
     */
    private $timesheetService;


    /**
     * @param ModelHasApproversService $modelHasApproversService
     * @param TimeSheetService $timesheetService
     * @param OvertimeInterface $overtime
     * @param NotificationService $notificationService
     * @param EmployeeInterface $employee
     */
    public function __construct(
        ModelHasApproversService $modelHasApproversService,
        TimeSheetService $timesheetService,
        OvertimeInterface $overtime,
        NotificationService $notificationService,
        EmployeeInterface $employee,
    ) {
        $this->modelHasApproversService = $modelHasApproversService;
        $this->timesheetService = $timesheetService;
        $this->overtime = $overtime;
        $this->notificationService = $notificationService;
        $this->employee = $employee;

        parent::__construct();
    }

    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new OverTime();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function setTransformers($data)
    {
        $collection = $data->getCollection();
        $allForm = collect($collection)->transformWith(new ManagerTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));

        return $allForm;
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

        $month = $this->request->month;
        $screenKey = $this->request->key_screen;
        $this->query = $this->overtime->queryFilter($this->query, $month, $screenKey);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleForm(Request $request, $id)
    {
        if (!Auth::user()->hasPermissionTo('overtime.manage') || !$this->overtime->checkIsApprover($id)) {
            return response()->json([
                'message' => __('message.not_permission'),
            ], 403);
        }

        $form = $this->overtime->show($id);

        if (!$form) {
            return response()->json([
                'message' => __('message.not_found'),
            ], 404);
        }

        if ($form->status === OverTime::STATUS['CANCEL']) {
            return response()->json([
                'message' => __('message.is_form_processed'),
            ], 406);
        }

        $action = $request->action;
        $companyId = Auth::user()->company_id;

        $dataNoti = [
            'model_id' => $form['id'],
            'model_type' => Notification::MODEL_TYPE['OVERTIME'],
            'type' => Notification::TYPE['REJECT'],
            'content' => 'REJECT',
            'receiver_id' => $form['employee_id'],
        ];



        $statusOfApprover = OverTime::STATUS['REJECTED'];

        switch ($action) {
            case ManagerController::ACTION['ACCEPT']:
                $statusOfApprover = OverTime::STATUS['APPROVED'];

                $dataNoti['type'] = Notification::TYPE['ACCEPT'];
                $dataNoti['content'] = 'ACCEPT';        break;
            case ManagerController::ACTION['REJECT-AFTER-ACCEPT'] && $form->status == OverTime::STATUS['APPROVED']:
                $dataNoti['content'] = 'REJECT AFTER ACCEPT';

                $form->status = OverTime::STATUS['REJECTED'];
                $form->timesheet_id = null;
                break;
            case ManagerController::ACTION['REJECT']:
                $form->timesheet_id = null;
                $form->status = OverTime::STATUS['REJECTED'];
                break;
            default:
                return response()->json([
                    'message' => 'Invalid action',
                ], 400);
        }
        $form->save();
        $dataNoti = $this->notificationService->store($dataNoti);
        $this->modelHasApproversService->updateStatus($id, Auth::user()->employee_id, get_class($form), $statusOfApprover);

        $this->afterChangeStatusForm($form, $companyId, $action);

        return response()->json([
            'message' => __('message.update_success'),
        ], 200);
    }

    /**
     * @param $form
     * @param $companyId
     * @param $action
     * @return void
     */
    public function afterChangeStatusForm($form, $companyId, $action)
    {
        switch ($action) {
            case ManagerController::ACTION['ACCEPT']:
                $isAvailableAccept = collect(Arr::pluck($form->approvers, 'status'))->every(function ($value) {
                    return $value == ModelHasApprovers::STATUS['APPROVED'];
                });
                //                $isAvailableAccept = true;
                if ($isAvailableAccept) {
                    $timesheet = $this->timesheetService->updateOrCreateWhenApprovalOTForm($form, $companyId, ManagerController::ACTION['ACCEPT']);

                    $form->status = OverTime::STATUS['APPROVED'];
                    $form->timesheet_id = $timesheet['id'];
                }

                break;
            case ManagerController::ACTION['REJECT-AFTER-ACCEPT']:
                $this->timesheetService->updateOrCreateWhenApprovalOTForm($form, $companyId, ManagerController::ACTION['REJECT-AFTER-ACCEPT']);

                break;
            default:
                break;
        }
        $form->save();
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

        if (!Auth::user()->hasPermissionTo('overtime.manage')) {
            $this->query->whereNull('employee_id');
        } else {
            $this->query->whereHas('approvers', function ($q) use ($employeeId) {
                $q->where(['approve_employee_id' => $employeeId]);
            });
        }

        $this->query->with('approvers.employee.information');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getApprovers()
    {
        return $this->employee->getEmployeesHasPermission('overtime.manage');
    }
}
