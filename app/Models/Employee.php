<?php

namespace App\Models;

use App\Models\Master\Branch;
use App\Models\Master\Department;
use App\Models\Master\Job;
use App\Models\Master\Position;
use App\Models\TimeSheet\TimeSheet;
use App\Models\TimeSheet\TimeSheetsLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    public static $deleteOnCascadeRelas = ['user'];

    public const STATUS = [
        'RETIREMENT' => 0,
        'FULL_TIME_EMPLOYEE' => 1,
    ];

    protected $fillable = [
        'card_number',
        'employee_code',
        'official_employee_date',
        'date_start_work',
        'position_id',
        'department_id',
        'branch_id',
        'company_id',
        'personal_information_id',
        'status',
    ];

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function personalInformation()
    {
        return $this->hasOne(PersonalInformation::class, 'id', 'personal_information_id');
    }

    public function jobPosition()
    {
        return $this->belongsTo(Job::class, 'position_id', 'id');
    }

    public function workingDepartment()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function timesheets()
    {
        return $this->hasMany(TimeSheet::class, 'employee_id', 'id');
    }

    public function timesheetsLogs()
    {
        return $this->hasMany(TimeSheetsLog::class, 'employee_id', 'id')
            ->orderBy('date_time');
    }

    public function information()
    {
        return $this->hasOne(PersonalInformation::class, 'id', 'personal_information_id');
    }

    public function bankAccount()
    {
        return $this->hasOne(BankAccount::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id', 'id');
    }

    public function department()
    {
        return $this->hasOne(Department::class, 'id', 'department_id');
    }

    public function branch()
    {
        return $this->hasOne(Branch::class, 'id', 'branch_id');
    }
}
