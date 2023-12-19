<?php

namespace App\Models\LaborContract;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaborContractHasAllowance extends Model
{
    use HasFactory;

    protected $fillable = [
        'labor_contract_id', 'allowance_id'
    ];

    public function allowance()
    {
        return $this->hasOne(Allowance::class, 'id', 'allowance_id');
    }
}
