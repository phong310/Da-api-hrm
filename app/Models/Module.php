<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $table = 'modules';
    protected $fillable = ['name', 'group_id'];

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'module_id');
    }
}
