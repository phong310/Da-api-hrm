<?php

namespace App\Models;

use App\Models\Form\LeaveForm;
use App\Models\Form\OverTime;
use App\Models\Form\RequestChangeTimesheet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    const STATUS = ['UNREAD' => 0, 'READ' => 1, 'NEW' => 2];
    const TYPE = ['CREAT' => 0, 'UPDATE' => 1, 'ACCEPT' => 2, 'REJECT' => 3, 'REQUEST_APPROVAL' => 4, 'REVIEW' => 5, 'PUBLIC' => 6];
    const MODEL_TYPE = [
        'LEAVE' => 'LeaveForm',
        'OVERTIME' => 'OverTime',
        'REQUEST_CHANGE_TIMESHEET' => 'RequestChangeTimesheet',
        'COMPENSATORY_LEAVE' => 'CompensatoryLeave',
        'SALARY_SHEET' => 'SalarySheet',
    ];

    protected $fillable = [
        'content',
        'sender_id',
        'receiver_id',
        'status',
        'model_type',
        'model_id',
        'type'
    ];

    public function sender()
    {
        return $this->belongsTo(Employee::class, 'sender_id', 'id');
    }

    public function receiver()
    {
        return $this->belongsTo(Employee::class, 'receiver_id', 'id');
    }
}
