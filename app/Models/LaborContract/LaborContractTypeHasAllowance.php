<?php

namespace App\Models\LaborContract;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaborContractTypeHasAllowance extends Model
{
    use HasFactory;

    protected $fillable = [
        'labor_contract_type_id', 'allowance_id'
    ];
}
