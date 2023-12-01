<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasFactory;

    protected $table = 'educations';

    protected $fillable = [
        'school_name',
        'from_date',
        'to_date',
        'description',
        'personal_information_id',
    ];

    public function personalInformation()
    {
        return $this->belongsTo(PersonalInformation::class);
    }
}
