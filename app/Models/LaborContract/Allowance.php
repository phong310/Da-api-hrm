<?php

namespace App\Models\LaborContract;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allowance extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'status', 'amount_of_money', 'company_id'
    ];

    const STATUS = ['INACTIVE' => 0, 'ACTIVE' => 1];
}
