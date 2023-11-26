<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DaysInWeek extends Model
{
    use HasFactory;

    protected $table = 'days_in_week';

    protected $fillable = [
        'name', 'symbol',
    ];

    // public function workingDay () {
    //     return $this->hasMany(WorkingDay::class, 'm_working_day');
    // }
}
