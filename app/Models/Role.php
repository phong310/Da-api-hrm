<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole
{
    use SoftDeletes;
    protected $fillable = [
        'name', 'guard_name', 'company_id',
    ];

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'role_has_modules');
    }
}
