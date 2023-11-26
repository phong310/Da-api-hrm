<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BaseMaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company_id',
    ];
}
