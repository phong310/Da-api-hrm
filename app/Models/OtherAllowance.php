<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherAllowance extends Model
{
    use HasFactory;

    protected $table = 'other_allowance';

    protected $fillable = [
        'name', 'company_id'
    ];
}
