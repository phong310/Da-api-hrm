<?php

namespace App\Models;

use App\Models\Form\LeaveForm;
use App\Models\Master\Branch;
use App\Models\Master\Department;
use App\Models\Master\Holiday;
use App\Models\Master\Position;
use App\Models\Master\Title;
use App\Models\Master\WorkingDay;
use App\Models\TimeSheet\TimeSheet;
use App\Models\TimeSheet\TimeSheetsLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS = [
        'NEW' => 0,
        'ACTIVE' => 1,
        'REJECT' => 2,
        'INACTIVE' => 3,
    ];

    protected $fillable = [
        'name',
        'representative',
        'logo',
        'type_of_business',
        'phone_number',
        'tax_code',
        'address',
        'status',
        'start_time',
        'end_time',
        'register_date',
    ];

    public $appends = ['logo_url'];

    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return Storage::disk('public')->url($this->logo);
        }
    }

    public function users()
    {
        return $this->hasMany(User::class, 'company_id');
    }

    public function positions()
    {
        return $this->hasMany(Position::class, 'company_id');
    }

    public function branches()
    {
        return $this->hasMany(Branch::class, 'company_id');
    }

    public function departments()
    {
        return $this->hasMany(Department::class, 'company_id');
    }

    public function titles()
    {
        return $this->hasMany(Title::class, 'company_id');
    }

    public function workingDays()
    {
        return $this->hasMany(WorkingDay::class, 'company_id');
    }

    public function holidays()
    {
        return $this->hasMany(Holiday::class, 'company_id');
    }

    public function leaveForms()
    {
        return $this->hasMany(LeaveForm::class, 'company_id');
    }

    public function timesheets()
    {
        return $this->hasMany(Timesheet::class, 'company_id');
    }

    public function timesheetsLogs()
    {
        return $this->hasMany(TimesheetsLog::class, 'company_id');
    }

    public function setting()
    {
        return $this->hasOne(Setting::class, 'company_id');
    }

    public function setIsForceDeleteAttribute($value)
    {
        $this->attributes['is_force_delete'] = $value;
    }
}
