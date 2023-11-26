<?php

namespace App\Models\Master;

use App\Models\Company;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends BaseMaster
{
    use SoftDeletes;

    protected $table = 'm_departments';

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}
