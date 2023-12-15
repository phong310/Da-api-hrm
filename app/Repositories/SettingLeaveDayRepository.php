<?php

namespace App\Repositories;

use App\Models\Setting\SettingLeaveDay;
use App\Repositories\Interfaces\SettingLeaveDayInterface;

class SettingLeaveDayRepository implements SettingLeaveDayInterface
{
    /**
     * @var SettingLeaveDay
     */
    protected $settingLeaveDay;

    /**
     * @param SettingLeaveDay $settingLeaveDay
     */
    public function __construct(SettingLeaveDay $settingLeaveDay)
    {
        $this->settingLeaveDay = $settingLeaveDay;
    }

    /**
     * @param $data
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed
     */
    public function store($data)
    {
        return $this->settingLeaveDay::query()->create($data);
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|object|null
     */
    public function show($id)
    {
        return $this->settingLeaveDay::query()->where(['id' => $id])->first();
    }

    public function update($data, $id)
    {
        $setting_leave_day = $this->show($id);

        if (!$setting_leave_day) {
            return null;
        }

        $setting_leave_day->fill($data);
        $setting_leave_day->save();

        return $setting_leave_day;
    }

    /**
     * @param $companyId
     * @param $type
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|object|null
     */
    public function showByType($companyId, $type)
    {
        return $this->settingLeaveDay::query()->where(['company_id' => $companyId, 'type' => $type])
            ->with(['positionHasSettingLeaveDay', 'departmentHasSettingLeaveDay'])
            ->first();
    }

    /**
     * @param $companyId
     * @param $positionId
     * @param $date
     * @return bool|mixed
     */
    public function checkPositionHasLeaveDay($companyId, $positionId, $date, $type)
    {
        return $this->settingLeaveDay::query()
            ->where([
                'company_id' => $companyId,
                'type' => $type,
            ])
            ->where(function ($q) use ($date) {
                $q->where(function ($q) {
                    $q->whereNull('applied_date')->whereNull('expired_date');
                })->orWhere(function ($q) use ($date) {
                    $q->where('applied_date', '<=', $date)->where('expired_date', '>=', $date);
                })->orWhere(function ($q) use ($date) {
                    $q->whereNull('applied_date')->where('expired_date', '>=', $date);
                })->orWhere(function ($q) use ($date) {
                    $q->where('applied_date', '<=', $date)->whereNull('expired_date');
                });
            })
            ->whereHas('positionHasSettingLeaveDay', function ($q) use ($positionId) {
                $q->where(['model_id' => $positionId]);
            })->exists();
    }

    /**
     * @param $companyId
     * @param $departmentId
     * @param $date
     * @param $type
     * @return mixed|void
     */
    public function checkDepartmentHasLeaveDay($companyId, $departmentId, $date, $type)
    {
        // TODO: Implement checkDepartmentHasLeaveDay() method.
    }
}
