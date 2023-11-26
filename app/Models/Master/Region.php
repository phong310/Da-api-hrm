<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use HasFactory;

    protected $table = 'm_regions';

    protected $fillable = ['name', 'code', 'level', 'parent_id'];

    public function region()
    {
        return $this->belongsTo(Region::class, 'parent_id');
    }
}
