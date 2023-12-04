<?php

namespace App\Models\TimeSheet;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Form\CompensatoryLeaveHasTimeSheet;
use App\Models\Form\LeaveFormHasTimeSheet;
use App\Models\Form\OverTime;
use App\Models\PersonalInformation;
use App\Traits\SystemSetting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeSheet extends Model
{
    use HasFactory, SoftDeletes, SystemSetting;

    const TIMESHEET_TYPE = [
        'NORMAL' => 1,
        'HOLIDAY' => 2,
    ];

    protected $table = 'timesheets';

    protected $fillable = [
        'employee_id',
        'start_time',
        'end_time',
        'type',
        'date',
        'company_id',
        'total_time_work',
        'late_time',
        'time_early',
        'real_end_time',
        'real_start_time',
        'real_total_time_work',
        'real_time_early',
        'real_late_time',
    ];

    public $timestamps = false;

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function personalInformation()
    {
        return $this->hasOneThrough(PersonalInformation::class, Employee::class, 'id', 'id', 'employee_id', 'personal_information_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    public function overtime()
    {
        return $this->hasOne(OverTime::class, 'timesheet_id');
    }

    public function leaveFormHasTimesheets()
    {
        return $this->hasMany(LeaveFormHasTimeSheet::class, 'timesheet_id');
    }

    public function compensatoryLeaveHasTimesheet()
    {
        return $this->hasOne(CompensatoryLeaveHasTimeSheet::class, 'timesheet_id');
    }
}
