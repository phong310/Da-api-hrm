<?php

namespace App\Models\LaborContract;

use App\Models\Employee;
use App\Models\Master\Branch;
use App\Models\Master\Position;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaborContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'code',
        'labor_contract_type_id',
        'effective_date',
        'expire_date',
        'sign_date',
        'status',
        'basic_salary',
        'insurance_salary',
        'is_social_insurance',
        'is_health_insurance',
        'is_syndicate',
        'is_unemployment_insurance',
        'note',
        'termination_date',
        'reason_contract_termination',
        'company_id',
        'hourly_salary',
        "is_system_insurance_salary"
    ];

    const STATUS = ['POSTPONE' => 0, 'ACTIVE' => 1, 'TERMINATE' => 2, 'EXTEND' => 3, 'EXPIRTION' => 4,];

    const EXPIRATION_DAY = 10;

    protected $hidden = ['created_at', 'updated_at', 'deleted_at'];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'id');
    }

    public function labor_contract_type()
    {
        return $this->belongsTo(LaborContractType::class, 'labor_contract_type_id', 'id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function allowances()
    {
        return $this->hasMany(LaborContractHasAllowance::class, 'labor_contract_id');
    }

    public function addresses()
    {
        return $this->hasMany(LaborContractAddress::class, 'labor_contract_id');
    }
}
