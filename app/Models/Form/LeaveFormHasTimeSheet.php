<?php

namespace App\Models\Form;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveFormHasTimeSheet extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'leave_form_has_timesheets';

    /**
     * @var string[]
     */
    protected $fillable = [
        'timesheet_id',
        'leave_form_id',
        'start_time',
        'end_time',
        'time_off',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function leaveForm()
    {
        return $this->belongsTo(LeaveForm::class, 'leave_form_id', 'id');
    }
}
