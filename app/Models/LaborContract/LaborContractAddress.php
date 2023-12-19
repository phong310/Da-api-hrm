<?php

namespace App\Models\LaborContract;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaborContractAddress extends Model
{
    use HasFactory;

    const TYPE = ['RESIDENT' => 0, 'DOMICILE' => 1];

    protected $fillable = [
        'province',
        'district',
        'ward',
        'address',
        'type',
        'labor_contract_id',
    ];
}
