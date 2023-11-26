<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use HasFactory, SoftDeletes;

    public static $deleteOnCascadeRelas = ['employee'];

    protected $fillable = [
        'account_number',
        'account_name',
        'bank_type',
        'bank_branch',
        'bank_name',
        'employee_id',
    ];

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }
}
