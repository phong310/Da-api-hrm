<?php

namespace App\Repositories;

use App\Http\Services\v1\Admin\CompanyService;
use App\Models\Form\OvertimeSalaryCoefficient;
use App\Repositories\Interfaces\HolidayInterface;
use App\Repositories\Interfaces\OvertimeSalaryCoefficientInterface;
use App\Repositories\Interfaces\SettingTypesOvertimeInterface;
use App\Repositories\Interfaces\WorkingDayInterface;
use App\Traits\CalculateTime;
use App\Traits\SystemSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OvertimeSalaryCoefficientRepository implements OvertimeSalaryCoefficientInterface
{
    use SystemSetting;
    use CalculateTime;

    /**
     * @var OvertimeSalaryCoefficient
     */
    protected $overtimeSalaryCoefficient;
    /**
     * @var WorkingDayInterface
     */
    protected $workingDay;
    /**
     * @var HolidayInterface
     */
    protected $holiday;
    /**
     * @var SettingTypesOvertimeInterface
     */
    protected $settingTypesOvertime;
    /**
     * @var CompanyService
     */
    protected $companyService;

    public function __construct(
        OvertimeSalaryCoefficient     $overtimeSalaryCoefficient,
        WorkingDayInterface           $workingDay,
        HolidayInterface              $holiday,
        SettingTypesOvertimeInterface $settingTypesOvertime,
        CompanyService                $companyService
    ) {
        $this->overtimeSalaryCoefficient = $overtimeSalaryCoefficient;
        $this->workingDay = $workingDay;
        $this->holiday = $holiday;
        $this->settingTypesOvertime = $settingTypesOvertime;
        $this->companyService = $companyService;
    }

    /**
     * @param $data
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function store($data)
    {
        return $this->overtimeSalaryCoefficient::query()->create($data);
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function show($id)
    {
        return $this->overtimeSalaryCoefficient::query()->where(['id' => $id])->first();
    }

    public function storeData($overtimeId, $startTime, $endTime, $salaryCoefficient)
    {
        $totalTimeWork = (new Carbon($startTime))->floatDiffInMinutes(new Carbon($endTime));
        if (!$totalTimeWork) {
            return;
        }

        return $this->store([
            'overtime_id' => $overtimeId,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'salary_coefficient' => $salaryCoefficient,
            'total_time_work' => $totalTimeWork
        ]);
    }

    public function storeByRangeTime($overtime)
    {
        $companyId = Auth::user()->company_id;
        $setting = $this->companyService->getSettingOfCompany($companyId);
        $timezone = $setting->time_zone;

        $date = new Carbon($overtime['date']);
        $dateString = Carbon::parse($overtime['date'])->format('Y-m-d');

        $startTime = $overtime['start_time'];
        $endTime = $overtime['end_time'];

        $settingTypeOvertime = $this->settingTypesOvertime->showByDate($companyId, $date);
        $settingOTSalaryCoefficient = $settingTypeOvertime->settingOvertimeSalaryCoefficient;

        $findRangeForStartTime = false;
        $findRangeForEndTime = false;

        foreach ($settingOTSalaryCoefficient as $value) {
            $settingStartTime = $dateString . ' ' . $value['start_time'];
            $settingEndTime = ($value['end_time'] == '00:00:00' ?
                date('Y-m-d', strtotime($dateString . ' + 1 days')) :
                $dateString) . ' ' . $value['end_time'];

            // TODO: Check nếu startTime và endTime đều nằm trong 1 khoảng thời gian
            if (
                $this->checkInRangeTime($settingStartTime, $settingEndTime, $startTime) &&
                $this->checkInRangeTime($settingStartTime, $settingEndTime, $endTime)
            ) {
                $this->storeData($overtime['id'], $startTime, $endTime, $value['salary_coefficient']);
                break;
            }

            // TODO: Check nếu startTime nằm trong 1 khoảng thời gian
            if ($this->checkInRangeTime($settingStartTime, $settingEndTime, $startTime)) {
                $this->storeData($overtime['id'], $startTime, $this->convertDateTimeToTZ($settingEndTime, $timezone), $value['salary_coefficient']);
                $findRangeForStartTime = true;
                continue;
            }

            // TODO: Check nếu endTime nằm trong 1 khoảng thời gian
            if ($this->checkInRangeTime($settingStartTime, $settingEndTime, $endTime)) {
                $this->storeData($overtime['id'], $this->convertDateTimeToTZ($settingStartTime, $timezone), $endTime, $value['salary_coefficient']);
                $findRangeForEndTime = true;
                continue;
            }

            // TODO: Kiêm tra xem đã tìm được khoảng cho startTime và endTime chưa => nếu chưa thì tạo bằng khoảng mình kiểm tra
            if (!$findRangeForStartTime && !$findRangeForEndTime) {

                // TODO: Với trường hợp [startTime,endTime] nằm ngoài khoảng [settingStartTime,settingEndTime] => skip đến mốc thời gian coefficient tiếp theo
                $settingStartTimeFormatted = Carbon::parse($settingStartTime)->format('Y-m-d H:i:s');
                $settingEndTimeFormatted = Carbon::parse($settingEndTime)->format('Y-m-d H:i:s');
                $convertStartTime = $this->convertDateTimeToTZ($startTime, 'UTC', $timezone)->format('Y-m-d H:i:s');
                $convertEndTime = $this->convertDateTimeToTZ($endTime, 'UTC', $timezone)->format('Y-m-d H:i:s');

                $isSkipThisCase = $convertStartTime >= $settingEndTimeFormatted || $convertEndTime <= $settingStartTimeFormatted;
                if ($isSkipThisCase) {
                    continue;
                }

                // TODO: Với trường hợp [startTime,endTime] bao bọc ngoài khoảng [settingStartTime,settingEndTime] => lưu khoảng [settingStartTime,settingEndTime] vào database
                $this->storeData($overtime['id'], $this->convertDateTimeToTZ($settingStartTime, $timezone), $this->convertDateTimeToTZ($settingEndTime, $timezone), $value['salary_coefficient']);
            }
        }
    }

    public function destroyByOvertimeId($overtimeId)
    {
        $this->overtimeSalaryCoefficient::query()->where('overtime_id', $overtimeId)->delete();
    }
}
