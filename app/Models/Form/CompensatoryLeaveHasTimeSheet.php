<?php

namespace App\Models\Form;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompensatoryLeaveHasTimeSheet extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'compensatory_leave_has_timesheets';

    /**
     * @var string[]
     */
    protected $fillable = [
        'timesheet_id',
        'compensatory_leave_id',
        'start_time',
        'end_time',
        'time_off',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function compensatoryLeave()
    {
        return $this->belongsTo(CompensatoryLeave::class, 'compensatory_leave_id', 'id');
    }
}
