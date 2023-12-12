<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingTypeOvertime extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'setting_types_overtime';

    const TYPE = [
        'AFTER_OFFICE_HOUR' => 1,
        'WEEKEND' => 2,
        'HOLIDAY' => 3,
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'company_id',
        'type',
    ];

    public function settingOvertimeSalaryCoefficient()
    {
        return $this->hasMany(SettingOvertimeSalaryCoefficient::class, 'setting_type_ot_id')->orderBy('start_time');
    }
}
