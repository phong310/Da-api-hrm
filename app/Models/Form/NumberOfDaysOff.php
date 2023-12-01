<?php

namespace App\Models\Form;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NumberOfDaysOff extends Model
{
    use HasFactory;

    const TYPE = [
        'ANNUAL_LEAVE' => 1,
        'LEAVE_FROM' => 2,
    ];

    protected $fillable = ['date', 'employee_id', 'number_of_minutes', 'type'];
    protected $table = 'number_of_days_off';
    public $timestamps = false;

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveForm()
    {
        return $this->hasOne(LeaveForm::class, 'number_of_days_off_id', 'id');
    }
}
