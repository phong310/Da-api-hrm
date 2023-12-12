<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingOvertimeSalaryCoefficient extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'setting_overtime_salary_coefficient';

    /**
     * @var string[]
     */
    protected $fillable = [
        'company_id',
        'setting_type_ot_id',
        'start_time',
        'end_time',
        'salary_coefficient'
    ];
}
