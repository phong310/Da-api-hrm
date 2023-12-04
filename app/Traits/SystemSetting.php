<?php

namespace App\Traits;

use Carbon\Carbon;

trait SystemSetting
{
    /**
     * @param $datetime
     * @param string $timezone
     * @return false|string
     */
    public function convertDateTime($datetime, $timezone = 'UTC')
    {
        $datetime = date_create($datetime);
        date_timezone_set($datetime, timezone_open($timezone));

        return date_format($datetime, config('format.datetime'));
    }

    /**
     * @param $dateTime
     * @param string $tzFrom
     * @param string $tzTo
     * @return Carbon
     */
    public function convertDateTimeToTZ($dateTime, $tzFrom = 'UTC', $tzTo = 'UTC')
    {
        return Carbon::parse($dateTime, $tzFrom)->setTimezone($tzTo);
    }

    /**
     * @param $dateTime
     * @return string
     */
    public function convertTZToDateTime($dateTime)
    {
        return Carbon::parse($dateTime)->toDateTimeString();
    }

    /**
     * @param $dataString
     * @return array|string|string[]|null
     */
    public function replaceSlashesToDashes($dataString)
    {
        if (!$dataString) {
            return null;
        }

        return str_replace('/', '-', $dataString);
    }
}
