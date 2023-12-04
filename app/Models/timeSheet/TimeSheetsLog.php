<?php

namespace App\Models\TimeSheet;

use App\Models\Company;
use App\Traits\SystemSetting;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeSheetsLog extends Model
{
    const TYPE = [
        'CHECK_IN' => 0,
        'CHECK_OUT' => 1,
    ];
    use HasFactory, SoftDeletes, SystemSetting;
    protected $table = 'timesheets_logs';
    protected $fillable = ['employee_id', 'date_time', 'type', 'note', 'company_id', "image_url", "longitude", "latitude"];
    // protected $primaryKey = ['employee_id', 'date', 'type'];
    public $timestamps = false;

    public function timesheet()
    {
        return $this->belongsTo(TimeSheet::class, 'timesheet_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}
