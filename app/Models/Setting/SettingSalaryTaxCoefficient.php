<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingSalaryTaxCoefficient extends Model
{
    use HasFactory;
    protected $table = 'salary_tax_coefficient_settings';

    protected $fillable = [
        'company_id',
        'currency',
        'amount_money_syndicate',
        'percent_social_insurance',
        'percent_medical_insurance',
        'percent_unemployment_insurance',
        'reduce_yourself',
        'family_allowances',
        'insurance_salary',
        'percent_syndicate'
    ];
}
