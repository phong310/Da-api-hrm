<?php

namespace App\Models\Master;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KindOfLeave extends Model
{
    use HasFactory, SoftDeletes;

    const TYPE = ['COMPENSATORY_LEAVE' => 0, 'NORMAL_LEAVE' => 1];

    protected $table = 'kind_of_leave';

    protected $fillable = [
        'name', 'symbol', 'company_id', 'type'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}
