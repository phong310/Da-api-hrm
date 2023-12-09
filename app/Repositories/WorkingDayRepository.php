<?php

namespace App\Repositories;

use App\Http\Services\v1\Admin\CompanyService;
use App\Http\Services\v1\Admin\CompensatoryWorkingDayService;
use App\Models\Master\WorkingDay;
use App\Repositories\Interfaces\WorkingDayInterface;
use App\Traits\SystemSetting;
use Carbon\Carbon;

class WorkingDayRepository implements WorkingDayInterface
{
    use SystemSetting;

    /**
     * @var WorkingDay
     */
    protected $workingDay;
    /**
     * @var CompanyService
     */
    protected $companyService;
    /**
     * @var CompensatoryWorkingDayService
     */
    protected $compensatoryWorkingDayService;

    /**
     * @param WorkingDay $workingDay
     * @param CompanyService $companyService
     * @param CompensatoryWorkingDayService $compensatoryWorkingDayService
     */
    public function __construct(
        WorkingDay $workingDay,
        CompanyService $companyService,
        CompensatoryWorkingDayService $compensatoryWorkingDayService
    ) {
        $this->workingDay = $workingDay;
        $this->companyService = $companyService;
        $this->compensatoryWorkingDayService = $compensatoryWorkingDayService;
    }

    /**
     * @param $data
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function store($data)
    {
        return $this->workingDay::query()->create($data);
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function show($id)
    {
        return $this->workingDay::query()->where(['id' => $id])->first();
    }

    /**
     * @param $data
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|string
     */
    public function stores($data)
    {
        $workingDay = '';
        foreach ($data['name'] as $key => $value) {
            $newValue = [];
            $newValue['name'] = $value;
            $newValue['day_in_week_id'] = $data['day_in_week_id'][$key];
            $newValue['type'] = $data['type'];
            $newValue['start_time'] = $data['start_time'];
            $newValue['end_time'] = $data['end_time'];
            $newValue['start_lunch_break'] = $data['start_lunch_break'];
            $newValue['end_lunch_break'] = $data['end_lunch_break'];
            $newValue['company_id'] = intval($data['company_id']);
            $workingDay = $this->store($newValue);
        }

        return $workingDay;
    }

    /**
     * @param $companyId
     * @param $date
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function showWorkingDayByDate($companyId, $date)
    {
        //N - The ISO-8601 numeric representation of a day (1 for Monday, 7 for Sunday)
        $code_day = date('N', strtotime($date));

        $wd = WorkingDay::query()->where([
            'company_id' => $companyId,
            'day_in_week_id' => $code_day,
        ])->first();

        $compensatoryWd = $this->compensatoryWorkingDayService->checkCompensatoryWorkingDayOfCompany($companyId, $date);

        return $compensatoryWd ?: $wd;
    }

    /**
     * @param $companyId
     * @param $dateTime
     * @return bool
     */
    public function isTimeInWorkingDay($companyId, $dateTime): bool
    {
        $date = Carbon::parse($dateTime)->format('Y-m-d');
        $time = $this->convertDateTimeToTZ($dateTime);

        $setting = $this->companyService->getSettingOfCompany($companyId);
        $timezone = $setting->time_zone;

        $workingDay = $this->showWorkingDayByDate($companyId, $date);

        if (!$workingDay) {
            return false;
        }

        $settingStartWork = $this->convertDateTimeToTZ($date . ' ' . $workingDay->start_time, $timezone);
        $settingEndWork = $this->convertDateTimeToTZ($date . ' ' . $workingDay->end_time, $timezone);

        return $time >= $settingStartWork && $time <= $settingEndWork;
    }
}
