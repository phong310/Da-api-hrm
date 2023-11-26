<?php

namespace App\Models\Master;

class Country extends BaseMaster
{
    protected $table = 'm_countries';

    protected $fillable = [
        'name',
    ];
}
