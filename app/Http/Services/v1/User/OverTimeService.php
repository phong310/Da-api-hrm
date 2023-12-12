<?php

namespace App\Http\Services\v1\User;

use App\Exports\OverTimeExport;
use App\Exports\OverTimeExportTemplate;
use App\Http\Services\v1\Admin\CompanyService;
use App\Http\Services\v1\Admin\HolidayService;
use App\Models\Form\OverTime;
use App\Models\Notification;
use App\Repositories\Interfaces\Forms\OvertimeInterface;
use App\Repositories\Interfaces\SettingTypesOvertimeInterface;
use App\Repositories\Interfaces\WorkingDayInterface;
use App\Traits\CalculateTime;
use App\Traits\FormSetting;
use App\Traits\SystemSetting;
use App\Transformers\OverTimeTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Maatwebsite\Excel\Facades\Excel;

class OverTimeService extends UserBaseService
{
    use SystemSetting;
    use FormSetting;
    use CalculateTime;
    /**
     * @var HolidayService
     */
    protected $holidayService;
    protected $workingDay;
    protected $overtime;
    protected $notificationService;
    protected $companyService;
    protected $settingTypesOvertime;
    /**
     * @param HolidayService $holidayService
     */
    public function __construct(
        HolidayService $holidayService,
        OvertimeInterface $overtime,
        WorkingDayInterface $workingDay,
        NotificationService $notificationService,
        CompanyService $companyService,
        SettingTypesOvertimeInterface $settingTypesOvertime
    ) {
        $this->holidayService = $holidayService;
        $this->workingDay = $workingDay;
        $this->overtime = $overtime;
        $this->notificationService = $notificationService;
        $this->companyService = $companyService;
        $this->settingTypesOvertime = $settingTypesOvertime;
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
        $overtime = collect($collection)->transformWith(new OverTimeTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));

        return $overtime;
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
     * @param $data
     * @return mixed
     */
    public function afterFind($data)
    {
        return $this->customDataApproval($data);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Http\JsonResponse|object
     */
    public function update(Request $request, $id)
    {
        $data = $request->only($this->model->getFillable());
        $overtime = $this->overtime->show($id);
        if (!$overtime) {
            return response()->json([
                'message' => __('message.not_found'),
            ], 404);
        }

        try {
            $overtime->fill($data);

            $overtime->save();

            return $overtime;
        } catch (\Exception $e) {
            Log::error($e);

            return $this->errorResponse();
        }
    }

    /**
     * @param Request $request
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $companyId = $request->user()->company_id;
        $data = $request->only($this->model->getFillable());

        $data['employee_id'] = Auth::user()->employee_id;
        $data['company_id'] = $companyId;
        $data['status'] = OverTime::STATUS['PROCESSING'];

        return $this->overtime->store($data);
    }

    /**
     * @param $date
     * @return mixed
     */
    public function coefficientSalaryOT($date)
    {
        $user = Auth::user();

        $companySetting = $user->company->setting;

        $coefficientSalary = $companySetting['OT_after_office_hours'];

        $companyId = $user->company_id;

        $holiday = $this->holidayService->checkHolidayOfCompany($companyId, $date);

        if ($holiday) {
            return $companySetting['OT_holiday'];
        }

        $workingDay = $this->workingDay->showWorkingDayByDate($companyId, $date);
        if (!$workingDay) {
            return $companySetting['OT_weekend'];
        }

        return $coefficientSalary;
    }

    public function cancel($id)
    {
        $overtimeForm = OverTime::find($id);

        if ($overtimeForm->status === OverTime::STATUS['PROCESSING']) {
            $this->notificationService->removeNotiOfApprovers($id, Notification::MODEL_TYPE['OVERTIME']);
            $overtimeForm->status = OverTime::STATUS['CANCEL'];

            $overtimeForm->save();

            return response()->json([
                'message' => __('message.update_success'),
                'data' => [
                    '$overtimeForm' => $overtimeForm
                ],
            ], 200);
        } else {
            return response()->json([
                'message' => __('message.is_form_processed'),
            ], 406);
        }
    }

    public function totalOverTime($startTime, $endTime)
    {
        return Carbon::parse($startTime)->floatDiffInMinutes(Carbon::parse($endTime));
    }

    // public function export()
    // {
    //     return Excel::download(new OverTimeExport(), 'overtime.xlsx');
    // }

    // public function exportTemplate()
    // {
    //     return Excel::download(new OverTimeExportTemplate(), 'overtime.xlsx');
    // }
}
