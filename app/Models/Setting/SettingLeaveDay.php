<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingLeaveDay extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'setting_leave_days';

    const TYPE = [
        'ANNUAL' => 1,
        'SENIORITY' => 2,
        'HOLIDAY' => 3,
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'company_id',
        'number_of_days',
        'applied_date',
        'expired_date',
        'min_working_time',
        'type',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function positionHasSettingLeaveDay()
    {
        return $this->hasMany(ModelHasSettingLeaveDay::class, 'model_setting_id', 'id')
            ->where(['model_type' => 'App\Models\Master\Position']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function departmentHasSettingLeaveDay()
    {
        return $this->hasMany(ModelHasSettingLeaveDay::class, 'model_setting_id', 'id')
            ->where(['model_type' => 'App\Models\Master\Department']);
    }
}
