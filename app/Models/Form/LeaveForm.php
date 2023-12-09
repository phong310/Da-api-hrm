<?php

namespace App\Models\Form;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Master\KindOfLeave;
use App\Traits\SystemSetting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveForm extends Model
{
    use HasFactory, SoftDeletes, SystemSetting;

    const STATUS = [
        'PROCESSING' => 0,
        'APPROVED' => 1,
        'REJECTED' => 2,
        'CANCEL' => 3,
    ];

    const PAID_LEAVE = [
        'NO' => 0,
        'YES' => 1,
    ];

    const KEY_SCREEN = [
        'AWAITING_CONFIRM' => 0,
        'PROCESSED' => 1,
    ];

    /**
     * @var string
     */
    protected $table = 'leave_form';

    /**
     * @var string[]
     */
    protected $fillable = [
        'employee_id',
        'kind_leave_id',
        'approval_deadline',
        'start_time',
        'end_time',
        'reason',
        'note',
        'status',
        'number_of_days_off_id',
        'is_salary',
        'company_id',
        'timesheet_id',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kind_of_leave()
    {
        return $this->belongsTo(KindOfLeave::class, 'kind_leave_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function number_of_days_off()
    {
        return $this->belongsTo(NumberOfDaysOff::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function approvers()
    {
        return $this->morphMany(ModelHasApprovers::class, 'model');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function leaveFormHasTimesheets()
    {
        return $this->hasMany(LeaveFormHasTimeSheet::class, 'leave_form_id', 'id');
    }
}
