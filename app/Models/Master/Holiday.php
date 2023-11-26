<?php

namespace App\Models\Master;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Holiday extends Model
{
    const TYPE = [
        'ANNUAL' => 1,
        'SINGLE_USE' => 2,
    ];
    use HasFactory, SoftDeletes;

    protected $table = 'holidays';

    protected $fillable = [
        'start_date',
        'end_date',
        'name',
        'company_id',
        'type',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}
