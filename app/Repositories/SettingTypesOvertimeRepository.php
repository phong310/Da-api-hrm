<?php

namespace App\Repositories;

use App\Models\Setting\SettingTypeOvertime;
use App\Repositories\Interfaces\HolidayInterface;
use App\Repositories\Interfaces\SettingTypesOvertimeInterface;
use App\Repositories\Interfaces\WorkingDayInterface;
use Illuminate\Support\Facades\Auth;

class SettingTypesOvertimeRepository implements SettingTypesOvertimeInterface
{
    /**
     * @var SettingTypeOvertime
     */
    protected $settingTypeOvertime;
    /**
     * @var WorkingDayInterface
     */
    protected $workingDay;
    /**
     * @var HolidayInterface
     */
    protected $holiday;

    /**
     * @param SettingTypeOvertime $settingTypeOvertime
     */
    public function __construct(
        SettingTypeOvertime $settingTypeOvertime,
        WorkingDayInterface $workingDay,
        HolidayInterface    $holiday
    ) {
        $this->settingTypeOvertime = $settingTypeOvertime;
        $this->workingDay = $workingDay;
        $this->holiday = $holiday;
    }

    /**
     * @param $data
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed
     */
    public function store($data)
    {
        return $this->settingTypeOvertime::query()->updateOrCreate($data, $data);
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|object|null
     */
    public function show($id)
    {
        return $this->settingTypeOvertime::query()->where(['id' => $id])->first();
    }

    /**
     * @param $data
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|object|null
     */
    public function update($data, $id)
    {
        $setting_type_overtime = $this->show($id);

        if (!$setting_type_overtime) {
            return null;
        }

        $setting_type_overtime->fill($data);
        $setting_type_overtime->save();

        return $setting_type_overtime;
    }

    /**
     * @param $companyId
     * @param $type
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|object|null
     */
    public function showByType($companyId, $type)
    {
        return $this->settingTypeOvertime::query()->where(['company_id' => $companyId, 'type' => $type])
            ->with(['settingOvertimeSalaryCoefficient'])
            ->first();
    }

    public function showByDate($companyId, $date)
    {
        $type = $this->settingTypeOvertime::TYPE['AFTER_OFFICE_HOUR'];

        if ($this->holiday->checkHolidayByDate($companyId, $date)) {
            $type = $this->settingTypeOvertime::TYPE['HOLIDAY'];
        } else if (!$this->workingDay->showWorkingDayByDate($companyId, $date)) {
            $type = $this->settingTypeOvertime::TYPE['WEEKEND'];
        }

        return $this->showByType($companyId, $type);
    }
}
