<?php

namespace App\Models\Setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelHasSettingLeaveDay extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'model_has_setting_leave_days';

    /**
     * @var string[]
     */
    protected $fillable = [
        'model_id',
        'model_type',
        'model_setting_id',
    ];
}
