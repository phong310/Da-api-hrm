<?php

namespace App\Models\Master;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkingDay extends Model
{
    use HasFactory, SoftDeletes;

    const TYPE = [
        'OFFICE_HOURS' => 1,
        'SHIFT_SYSTEM' => 2,
    ];

    protected $table = 'm_working_day';

    protected $fillable = [
        'name',
        'type',
        'start_time',
        'start_lunch_break',
        'end_lunch_break',
        'end_time',
        'day_in_week_id',
        'company_id',
        'total_working_time',
    ];

    public function dayInWeek()
    {
        return $this->belongsTo(DaysInWeek::class, 'day_in_week_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}
