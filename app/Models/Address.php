<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    const TYPE = ['RESIDENT' => 0, 'DOMICILE' => 1];

    protected $fillable = [
        'province',
        'district',
        'ward',
        'address',
        'type',
        'personal_information_id',
    ];

    public function personalInformation()
    {
        return $this->belongsTo(PersonalInformation::class);
    }
}
