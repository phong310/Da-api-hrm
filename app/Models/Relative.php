<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Relative extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'birthday',
        'relationship_type',
        'ward',
        'address',
        'district',
        'province',
        'phone',
        'employee_id',
        'sex',
        'is_dependent_person',
        'date_apply'
    ];

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }
}
