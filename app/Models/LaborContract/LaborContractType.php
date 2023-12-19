<?php

namespace App\Models\LaborContract;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaborContractType extends Model
{

    const STATUS_HOLIDAY = [
        'REJECTED' => 0,
        'APPLY' => 1
    ];

    use HasFactory;

    protected $fillable = [
        'name', 'company_id', 'duration_of_contract', 'status_apply_holiday'
    ];

    protected $hidden = ['created_at', 'updated_at', 'delete_at'];

    public function allowances()
    {
        return $this->belongsToMany(Allowance::class, 'labor_contract_type_has_allowances', 'labor_contract_type_id', 'allowance_id')
            ->where('status', Allowance::STATUS['ACTIVE']);
    }

    public function laborContractTypeHasAllowances()
    {
        return $this->hasMany(LaborContractTypeHasAllowance::class);
    }

    public function laborContract()
    {
        return $this->hasMany(LaborContract::class);
    }
}
