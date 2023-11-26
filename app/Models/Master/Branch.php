<?php

namespace App\Models\Master;

use App\Models\Company;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends BaseMaster
{
    use SoftDeletes;

    protected $table = 'm_branches';
    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}
