<?php

namespace App\Http\Services\v1\User;

use App\Http\Services\v1\Admin\CompanyService;
use App\Http\Services\v1\Admin\TimeSheetService;
use App\Models\Form\LeaveForm;
use App\Models\Form\RequestChangeTimesheet;
use App\Models\Notification;
use App\Repositories\Interfaces\WorkingDayInterface;
use App\Traits\CalculateTime;
use App\Traits\FormSetting;
use App\Traits\SystemSetting;
use App\Transformers\RequestChangeTimesheetTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class RequestChangeTimesheetService extends UserBaseService
{
    use FormSetting;
    use SystemSetting;
    use CalculateTime;

    protected $notificationService;
    protected $companyService;
    protected $workingDay;
    protected $timeSheetService;

    public function __construct(
        NotificationService  $notificationService,
        CompanyService $companyService,
        WorkingDayInterface $workingDay,
        TimeSheetService $timeSheetService
    ) {
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
        $this->model = new RequestChangeTimesheet();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function setTransformers($data)
    {
        $collection = $data->getCollection();
        foreach ($collection as $c) {
            $totalTimeOff = $this->totalRequestChangeTime($c['check_in_time'], $c['check_out_time'], $c['date']);
            $c['total_time'] = $totalTimeOff;
        }
        return collect($collection)->transformWith(new RequestChangeTimesheetTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));
    }

    /**
     * @return mixed
     */
    public function checkMySelf()
    {
        $employeeId = Auth::user()->employee_id;
        $this->query->where(['employee_id' => $employeeId])
            ->with(['approvers.employee.information', 'timeSheet']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function appendFilter()
    {
        $employee_id = Auth::user()->employee_id;
        $full_name = $this->request->get('approver');
        $status = $this->request->get('status');
        $date = $this->request->get('date');
        $yearMonth = $this->request->get('month');

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

        if (!is_null($date)) {
            $this->query->whereDate('date', $date);
        }

        if (!is_null($yearMonth)) {
            $yearMonthArr = explode('-', $yearMonth);
            $month = $yearMonthArr[1];
            $year = $yearMonthArr[0];
            $this->query
                ->whereYear('date', '=', $year)
                ->whereMonth('date', '=', $month);
        }


        $this->query
            ->where('employee_id', $employee_id)
            ->with('approvers.employee.information')
            ->orderBy('date', 'DESC');
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Http\JsonResponse|object
     */
    public function update(Request $request, $id)
    {
        $requestChange = $this->query->where('id', $id)->first();
        if (!$requestChange) {
            return response()->json([
                'message' => __('message.not_found'),
            ], 404);
        }
        try {
            $data = $request->only($this->model->getFillable());
            $requestChange->fill($data);
            $requestChange->save();

            return $requestChange;
        } catch (\Exception $e) {
            Log::error($e);

            return $this->errorResponse();
        }
    }

    /**
     * @param $data
     * @return mixed
     */
    public function afterFind($data)
    {
        $data['total_time'] = $this->totalRequestChangeTime($data['check_in_time'], $data['check_out_time']);
        return $this->customDataApproval($data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $companyId = $request->user()->company_id;
        try {
            $requestChangeTimesheet = RequestChangeTimesheet::create([
                'employee_id' => $this->request->user()->employee_id,
                'created_at' => $request->created_at,
                'check_in_time' => $data['check_in_time'],
                'check_out_time' => $data['check_out_time'],
                'status' => RequestChangeTimesheet::STATUS['PROCESSING'],
                'note' => isset($request->note) ? $request->note : null,
                'date' => $data['date'],
                'timesheet_id' => isset($request->timesheet_id) ? $request->timesheet_id : null,
                'company_id' => $companyId,
            ]);

            return $requestChangeTimesheet;
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    /**
     * @param $timesheet_id
     * @return mixed
     */
    public function getByTimesheetId($timesheet_id)
    {
        $requestChangeTimesheet = RequestChangeTimesheet::where('timesheet_id', $timesheet_id)->first();

        return $requestChangeTimesheet;
    }

    public function cancel($id)
    {
        $rctForm = RequestChangeTimesheet::find($id);

        if ($rctForm->status === RequestChangeTimesheet::STATUS['PROCESSING']) {
            $this->notificationService->removeNotiOfApprovers($id, Notification::MODEL_TYPE['REQUEST_CHANGE_TIMESHEET']);
            $rctForm->status = RequestChangeTimesheet::STATUS['CANCEL'];

            $rctForm->save();

            return response()->json([
                'message' => __('message.update_success'),
                'data' => [
                    'requestChangeTimesheetForm' => $rctForm
                ],
            ], 200);
        } else {
            return response()->json([
                'message' => __('message.is_form_processed'),
            ], 406);
        }
    }

    public function totalRequestChangeTime($checkInTime, $checkOutTime)
    {
        $company_id = Auth::user()->company_id;
        $setting = $this->companyService->getSettingOfCompany($company_id);
        $totalTime = $this->convertData($checkInTime, $checkOutTime, [], $setting);
        $total = 0;

        foreach ($totalTime as $t) {
            $total += $t['time_off'];
        }

        return $total;
    }
}
