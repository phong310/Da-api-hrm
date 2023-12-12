<?php

namespace App\Models\Form;

use App\models\Employee;
use App\Models\TimeSheet\TimeSheet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestChangeTimesheet extends Model
{
    use HasFactory;
    protected $table = 'requests_change_timesheets';
    protected $fillable = [
        'employee_id', 'check_in_time', 'check_out_time',
        'timesheet_id', 'note', 'status', 'date', 'company_id',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

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

    // public $timestamps = false;

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function timesheet()
    {
        return $this->belongsTo(TimeSheet::class);
    }

    public function approvers()
    {
        return $this->morphMany(ModelHasApprovers::class, 'model');
    }
}
