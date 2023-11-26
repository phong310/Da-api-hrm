<?php

namespace App\Http\Services\v1\User;

use App\Http\Services\v1\BaseService;
use Carbon\Carbon;

abstract class UserBaseService extends BaseService
{
    /**
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    protected function timezone()
    {
        return config('app.timezone');
    }

    /**
     * @param string $date_time
     * @return string
     */
    protected function parseDate($date_time = '')
    {
        if (!$date_time) {
            return Carbon::now($this->timezone())->toDateString();
        }

        return (new Carbon($date_time))->toDateString();
    }

    /**
     * @param string $date_time
     * @return Carbon|string
     */
    protected function parseDateTime($date_time = '')
    {
        if (!$date_time) {
            return Carbon::now();
        }

        return (new Carbon($date_time))->toDateTimeString();
    }
}
