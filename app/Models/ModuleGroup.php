<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleGroup extends Model
{
    protected $table = 'module_groups';
    protected $fillable = ['name'];

    public function modules()
    {
        return $this->hasMany(Module::class, 'group_id');
    }
}
