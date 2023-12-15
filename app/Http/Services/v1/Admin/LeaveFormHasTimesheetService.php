<?php

namespace App\Http\Services\v1\Admin;

use App\Http\Services\v1\BaseService;
use App\Models\Form\LeaveFormHasTimeSheet;
use App\Repositories\Interfaces\WorkingDayInterface;
use App\Traits\CalculateTime;
use App\Traits\SystemSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LeaveFormHasTimesheetService extends BaseService
{
    use SystemSetting;
    use CalculateTime;
    /**
     * @var TimeSheetService
     */
    public $timeSheetService;
    /**
     * @var CompanyService
     */
    public $companyService;
    /**
     * @var HolidayService
     */
    public $holidayService;
    /**
     * @var WorkingDayInterface
     */
    public $workingDay;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        TimeSheetService $timeSheetService,
        CompanyService $companyService,
        HolidayService $holidayService,
        WorkingDayInterface $workingDay
    ) {
        $this->timeSheetService = $timeSheetService;
        $this->companyService = $companyService;
        $this->holidayService = $holidayService;
        $this->workingDay = $workingDay;
        parent::__construct();
    }

    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new LeaveFormHasTimeSheet();
    }

    /**
     * @param $leaveForm
     */
    public function store($leaveForm)
    {
        $startTime = $leaveForm->start_time;
        $endTime = $leaveForm->end_time;
        $employeeId = $leaveForm->employee_id;
        $isSalary = $leaveForm->is_salary;
        $leaveFormId = $leaveForm->id;

        $companyId = $this->getCompanyId();
        $setting = $this->companyService->getSettingOfCompany($companyId);
        $data = $this->convertData($startTime, $endTime, [], $setting);

        foreach ($data as $d) {
            $startTime = $d['start_time'];
            $endTime = $d['end_time'];
            $date = (new Carbon(strtotime($startTime)))->toDateString();

            $dataTimesheet = [
                'company_id' => $companyId,
                'employee_id' => $employeeId,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'time_off' => $d['time_off'],
                'date' => $date,
                'is_salary' => $isSalary,
            ];

            $workingDay = $this->workingDay->showWorkingDayByDate($companyId, $startTime);

            $timesheet = $this->timeSheetService->updateOrCreateData($dataTimesheet, $workingDay, $setting, 'leave-form');

            $d['timesheet_id'] = $timesheet->id;
            $d['leave_form_id'] = $leaveFormId;

            $item = new LeaveFormHasTimeSheet();
            foreach ($d as $key => $val) {
                $item->$key = $val;
            }
            $item->save();
        }
    }

    /**
     * @param Request $request
     * @param $form
     * @return mixed
     */
    public function update(Request $request, $form)
    {
        $data = $request->only($this->model->getFillable());
        $modelHasApprovers = $form->update($data);

        return $modelHasApprovers;
    }
}
