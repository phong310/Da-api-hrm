<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtherAmountOfAllowance extends Model
{
    use HasFactory;

    protected $table = 'other_amount_of_allowance';

    protected $fillable = [
        'salary_id',
        'other_allowance_id',
        'amount_of_money',
    ];

    // public function salary()
    // {
    //     return $this->belongsTo(Salary::class, 'salary_id', 'id');
    // }

    public function otherAllowance()
    {
        return $this->belongsTo(OtherAllowance::class, 'other_allowance_id', 'id');
    }
}
