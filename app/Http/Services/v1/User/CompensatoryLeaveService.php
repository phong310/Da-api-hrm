<?php

namespace App\Http\Services\v1\User;

use App\Http\Services\v1\Admin\CompanyService;
use App\Http\Services\v1\Admin\TimeSheetService;
use App\Models\Form\CompensatoryLeave;
use App\Models\Notification;
use App\Repositories\Interfaces\Forms\CompensatoryLeaveInterface;
use App\Repositories\Interfaces\WorkingDayInterface;
use App\Traits\CalculateTime;
use App\Traits\FormSetting;
use App\Traits\SystemSetting;
use App\Transformers\CompensatoryLeaveTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class CompensatoryLeaveService extends UserBaseService
{
    use SystemSetting;
    use FormSetting;
    use CalculateTime;

    /**
     * @var CompensatoryLeaveInterface
     */
    private $compensatoryLeave;
    protected $notificationService;
    protected $companyService;
    protected $workingDay;
    protected $timeSheetService;

    public function __construct(
        CompensatoryLeaveInterface $compensatoryLeave,
        NotificationService  $notificationService,
        CompanyService $companyService,
        WorkingDayInterface $workingDay,
        TimeSheetService $timeSheetService
    ) {
        $this->compensatoryLeave = $compensatoryLeave;
        $this->notificationService = $notificationService;
        $this->companyService = $companyService;
        $this->workingDay = $workingDay;
        $this->timeSheetService = $timeSheetService;
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
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function appendFilter()
    {
        $employee_id = Auth::user()->employee_id;
        $full_name = $this->request->get('approver');
        $kind_leave_id = $this->request->get('kind_of_leave');
        $status = $this->request->get('status');
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

        if (!is_null($startTime)) {
            $this->query->whereDate('start_time', $startTime);
        }

        if (!is_null($endTime)) {
            $this->query->whereDate('end_time', $endTime);
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
        return collect($collection)->transformWith(new CompensatoryLeaveTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));
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

    /**
     * @return mixed
     */
    public function checkMySelf()
    {
        $employeeId = Auth::user()->employee_id;
        $this->query->where(['employee_id' => $employeeId])
            ->with('approvers.employee.information');
    }


    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Http\JsonResponse|object
     */
    public function update(Request $request, $id)
    {
        $compensatoryLeave = $this->compensatoryLeave->showByEmployee($id);
        if (!$compensatoryLeave) {
            return response()->json([
                'message' => __('message.not_found'),
            ], 404);
        }
        try {

            $data = $request->only($this->model->getFillable());
            $compensatoryLeave->fill($data);
            $compensatoryLeave->save();

            return $compensatoryLeave;
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

        return $this->compensatoryLeave->store($data);
    }

    public function cancel($id)
    {

        $compensatoryLeaveForm = CompensatoryLeave::find($id);

        if ($compensatoryLeaveForm->status === CompensatoryLeave::STATUS['PROCESSING']) {
            $this->notificationService->removeNotiOfApprovers($id, Notification::MODEL_TYPE['LEAVE']);
            $compensatoryLeaveForm->status = CompensatoryLeave::STATUS['CANCEL'];
            $compensatoryLeaveForm->save();

            return response()->json([
                'message' => __('message.update_success'),
                'data' => [
                    'compensatoryLeaveForm' => $compensatoryLeaveForm
                ],
            ], 200);
        } else {
            return response()->json([
                'message' => __('message.is_form_processed'),
            ], 406);
        }
    }

    public function totalTimeOff($startTime, $endTime)
    {
        $company_id = Auth::user()->company_id;
        $setting = $this->companyService->getSettingOfCompany($company_id);
        $totalTime = $this->convertData($startTime, $endTime, [], $setting);
        $totalTimeOff = 0;

        foreach ($totalTime as $t) {
            $totalTimeOff += $t['time_off'];
        }

        return $totalTimeOff;
    }
}
