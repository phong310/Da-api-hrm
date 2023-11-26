<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdentificationCard extends Model
{
    use HasFactory;

    public static $Type = ['CMT' => 0, 'TCC' => 1];

    protected $fillable = [
        'ID_no',
        'issued_date',
        'issued_by',
        'ID_expire',
        'type',
        'personal_information_id',
    ];

    public function personalInformation()
    {
        return $this->belongsTo(PersonalInformation::class);
    }
}
