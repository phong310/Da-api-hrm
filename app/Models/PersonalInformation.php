<?php

namespace App\Models;

use App\Models\Master\Country;
use App\Models\Master\EducationLevel;
use App\Models\Master\Job;
use App\Models\Master\Title;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class PersonalInformation extends Model
{
    use HasFactory, SoftDeletes;

    protected $appends = ['full_name'];

    public static $deleteOnCascadeRelas = ['employee'];

    public static $SexType = ['Male' => 1, 'Female' => 0];
    public static $MaritalStatusType = ['Single' => 1, 'Married' => 0];

    protected $fillable = [
        'first_name',
        'last_name',
        'job_id',
        'nickname',
        'birthday',
        'marital_status',
        'sex',
        'education_level_id',
        'title_id',
        'email',
        'phone',
        'note',
        'country_id',
        'ethnic',
    ];



    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function identificationCards()
    {
        return $this->hasMany(IdentificationCard::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'personal_information_id', 'id');
    }

    public function educations()
    {
        return $this->hasMany(Education::class);
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function educationLevel()
    {
        return $this->belongsTo(EducationLevel::class);
    }

    public function title()
    {
        return $this->belongsTo(Title::class);
    }

    public function getThumbnailUrlAttribute($value)
    {
        if ($value) {
            return Storage::disk('public')->url($value);
        };

        return null;
    }
}
