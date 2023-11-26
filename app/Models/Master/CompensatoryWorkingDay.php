<?php

namespace App\Models\Master;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompensatoryWorkingDay extends Model
{
    use HasFactory;

    const TYPE = [
        'ANNUAL' => 1,
        'SINGLE_USE' => 2,
    ];

    protected $fillable = [
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'start_lunch_break',
        'end_lunch_break',
        'name',
        'company_id',
        'type',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}
