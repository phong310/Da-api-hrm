<?php

namespace App\Models\Form;

use App\Models\Employee;
use App\Traits\SystemSetting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OverTime extends Model
{
    use HasFactory, SystemSetting;

    const STATUS = [
        'PROCESSING' => 0,
        'APPROVED' => 1,
        'REJECTED' => 2,
        'CANCEL' => 3,
    ];

    const KEY_SCREEN = [
        'AWAITING_CONFIRM' => 0,
        'PROCESSED' => 1,
    ];

    protected $table = 'over_times';

    protected $fillable = [
        'employee_id',
        'start_time',
        'end_time',
        'date',
        'reason',
        'note',
        'status',
        'total_time_work',
        'timesheet_id',
        'company_id',
        // 'coefficient_salary',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function approvers()
    {
        return $this->morphMany(ModelHasApprovers::class, 'model');
    }

    public function overtimeSalaryCoefficients()
    {
        return $this->hasMany(OvertimeSalaryCoefficient::class, 'overtime_id');
    }
}
