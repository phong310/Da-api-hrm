<?php

namespace App\Http\Services\v1\User;

use App\Http\Services\v1\Admin\CompanyService;
use App\Http\Services\v1\Admin\TimeSheetService;
use App\Models\Form\LeaveForm;
use App\Models\Notification;
use App\Repositories\Interfaces\Forms\LeaveFormInterface;
use App\Repositories\Interfaces\WorkingDayInterface;
use App\Traits\CalculateTime;
use App\Traits\FormSetting;
use App\Traits\SystemSetting;
use App\Transformers\LeaveAppInfoTransformer;
use App\Transformers\LeaveFormTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class LeaveFormService extends UserBaseService
{
    use SystemSetting;
    use FormSetting;
    use CalculateTime;
    protected $leaveForm;
    protected $numberOfDaysOff;

    /**
     * @var NotificationService
     */
    protected $notificationService;

    /**
     * Instantiate a new controller instance.
     *
     * @param LeaveFormInterface $leaveForm
     */

    protected $companyService;
    protected $workingDay;
    protected $timeSheetService;
    public function __construct(
        LeaveFormInterface $leaveForm,
        NotificationService $notificationService,
        CompanyService $companyService,
        WorkingDayInterface $workingDay,
        // TimeSheetService $timeSheetService
    ) {
        $this->leaveForm = $leaveForm;
        $this->notificationService = $notificationService;
        $this->companyService = $companyService;
        $this->workingDay = $workingDay;
        // $this->timeSheetService = $timeSheetService;
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
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function appendFilter()
    {
        $employee_id = Auth::user()->employee_id;
        $full_name = $this->request->get('approver');
        $kind_leave_id = $this->request->get('kind_of_leave');
        $status = $this->request->get('status');
        $is_salary = $this->request->get('is_salary');
        $startTime = $this->request->get('start_time');
        $endTime = $this->request->get('end_time');
        $yearMonth = $this->request->get('month');

        if (!is_null($kind_leave_id)) {
            $this->query->where('kind_leave_id', $kind_leave_id);
        }

        if ($full_name) {
            $this->query->whereHas('approvers', function ($q) use ($full_name) {
                $q->whereHas('employee', function ($q) use ($full_name) {
                    $q->whereHas('personalInformation', function ($q) use ($full_name) {
                        $q->whereRaw(
                            "TRIM(CONCAT(first_name, ' ', last_name)) like '%{$full_name}%'"
                        );
                    });
                });
            });
        }

        if (!is_null($status)) {
            $this->query->where('status', $status);
        }

        if (!is_null($is_salary)) {
            $this->query->where('is_salary', $is_salary);
        }

        if (!is_null($startTime) && is_null($endTime)) {
            $this->query->whereDate('start_time', $startTime);
        }

        if (!is_null($endTime) && is_null($startTime)) {
            $this->query->whereDate('end_time', $endTime);
        }

        if (!is_null($startTime) && !is_null($endTime)) {
            $this->query->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime]);
            });
        }

        if (!is_null($yearMonth)) {
            $yearMonthArr = explode('-', $yearMonth);
            $month = $yearMonthArr[1];
            $year = $yearMonthArr[0];
            $this->query
                ->whereYear('start_time', '=', $year)
                ->whereMonth('start_time', '=', $month);
        }

        $this->query
            ->where('employee_id', $employee_id)
            ->with('approvers.employee.information')
            ->orderBy('start_time', 'DESC');
    }

    /**
     * @param $data
     * @return mixed
     */
    public function setTransformers($data)
    {
        $collection = $data->getCollection();
        foreach ($collection as $c) {
            $totalTimeOff = $this->totalTimeOff($c['start_time'], $c['end_time']);
            $c['total_time_off'] = $totalTimeOff;
        }
        return collect($collection)->transformWith(new LeaveFormTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function afterFind($data)
    {
        $totalTimeOff = $this->totalTimeOff($data['start_time'], $data['end_time']);
        $data['total_time_off'] = $totalTimeOff;
        return $this->customDataApproval($data);
    }

    public function totalTimeOff($startTime, $endTime)
    {
        // $company_id = Auth::user()->company_id;
        // $setting = $this->companyService->getSettingOfCompany($company_id);
        // $totalTime = $this->convertData($startTime, $endTime, [], $setting);
        // $totalTimeOff = 0;

        // foreach ($totalTime as $t) {
        //     $totalTimeOff += $t['time_off'];
        // }

        return 480;
    }

    /**
     * @return mixed
     */
    public function checkMySelf()
    {
        $employeeId = Auth::user()->employee_id;
        $this->query->where(['employee_id' => $employeeId])
            ->with('approvers.employee.information');
    }

    public function hasPermApproval($employeeId)
    {
        where(function ($q) use ($employeeId) {
            $q->whereHas('approvers', function ($q) use ($employeeId) {
                $q->where(['approve_employee_id' => $employeeId]);
            });
        });
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Http\JsonResponse|object
     */
    public function update(Request $request, $id)
    {
        $leaveForm = $this->leaveForm->showByEmployee($id);
        if (!$leaveForm) {
            return response()->json([
                'message' => __('message.not_found'),
            ], 404);
        }
        try {

            $data = $request->only($this->model->getFillable());
            $leaveForm->fill($data);
            $leaveForm->save();

            return $leaveForm;
        } catch (\Exception $e) {
            return $this->errorResponse();
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        if (!$request->user()->employee_id) {
            return response()->json([
                'message' => __('message.not_found'),
            ], 404);
        }

        $data = [
            'employee_id' => $request->user()->employee_id,
            'company_id' => $request->user()->company_id,
            'approval_deadline' => $request->approval_deadline,
            'kind_leave_id' => $request->kind_leave_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'reason' => $request->reason,
            'note' => $request->note,
            'status' => 0,
        ];

        if ($request->is_salary) {
            $data['is_salary'] = LeaveForm::PAID_LEAVE['YES'];
        } else {
            $data['is_salary'] = LeaveForm::PAID_LEAVE['NO'];
        }
        //    $data['number_of_days_off_id'] = $numberDaysOff->id;

        $leaveForm = LeaveForm::create($data);

        return $leaveForm;
    }

    public function cancel($id)
    {
        $leaveForm = LeaveForm::find($id);

        if ($leaveForm->status === LeaveForm::STATUS['PROCESSING']) {
            $this->notificationService->removeNotiOfApprovers($id, Notification::MODEL_TYPE['LEAVE']);
            $leaveForm->status = LeaveForm::STATUS['CANCEL'];
            $leaveForm->save();

            return response()->json([
                'message' => __('message.update_success'),
                'data' => [
                    'leaveForm' => $leaveForm
                ],
            ], 200);
        } else {
            return response()->json([
                'message' => __('message.is_form_processed'),
            ], 406);
        }
    }

    public function getProcessLeaveForm()
    {
        $employee_id = Auth::user()->employee_id;
        $data = $this->leaveForm->queryFormHasProcessing($employee_id);

        return $data->map(function ($item) {
            return [
                'start_time' => $item->start_time,
                'end_time' => $item->end_time,
            ];
        });
    }

    public function calculateLeaveFormProcess()
    {
        $data = $this->getProcessLeaveForm();
        $totalLeaveformProcess = 0;

        foreach ($data as $d) {
            $cal = $this->totalTimeOff($d['start_time'], $d['end_time']);
            $totalLeaveformProcess += $cal;
        }
        return $totalLeaveformProcess;
    }
}
