<?php

namespace App\Models\Master;

use App\Models\Company;
use Illuminate\Database\Eloquent\SoftDeletes;

class Title extends BaseMaster
{
    use SoftDeletes;

    protected $table = 'm_titles';

    protected $fillable = [
        'name', 'company_id'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id');
    }
}
