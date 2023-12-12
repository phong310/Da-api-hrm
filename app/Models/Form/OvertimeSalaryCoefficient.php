<?php

namespace App\Models\Form;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OvertimeSalaryCoefficient extends Model
{
    use HasFactory;

    protected $table = 'overtime_salary_coefficient';

    protected $fillable = ['overtime_id', 'start_time', 'end_time', 'salary_coefficient', 'total_time_work'];
}
