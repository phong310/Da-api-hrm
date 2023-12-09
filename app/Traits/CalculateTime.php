<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

trait CalculateTime
{
    static $PRIORITY = [
        "TIMESHEET" => 9999,
    ];

    /**
     * @param $startTime
     * @param $endTime
     * @param $data
     * @param $setting
     * @return mixed
     * @throws \Exception
     */
    function convertData($startTime, $endTime, $data, $setting)
    {
        $companyId = $this->getCompanyId();
        $startAtTZ = $this->convertDateTimeToTZ($startTime, 'UTC', $setting->time_zone);
        $endAtTZ = $this->convertDateTimeToTZ($endTime, 'UTC', $setting->time_zone);
        $workingDay = $this->workingDay->showWorkingDayByDate($companyId, $startTime);

        if ($startAtTZ->format('Y-m-d') == $endAtTZ->format('Y-m-d') && $workingDay) {
            $date = (new Carbon(strtotime($startTime)))->toDateString();
            $data[] = [
                'start_time' => $startTime,
                'end_time' => $endTime,
                'time_off' => $this->timeSheetService->getTotalTimeWork($endTime, $startTime, $workingDay, $setting, $date)['total_time_work'],
            ];

            return $data;
        }

        $startDateTZ = new \DateTime($startAtTZ->toDateString());
        $endDateTZ = new \DateTime($endAtTZ->toDateString());
        $diff = $endDateTZ->diff($startDateTZ);

        $startDay = $this->convertDateTimeToTZ($startTime);

        for ($diffDay = 0; $diffDay <= $diff->days; $diffDay++) {

            $startDaysStr = Carbon::parse($startDay->toDateString())->addDays($diffDay)->toDateString();
            $startAt = $startTime;
            $endAt = $endTime;

            $startTimeWorkDay = Carbon::parse($startDay->toDateString())->addDays($diffDay)->toDateTimeString();
            $workingDay = $this->workingDay->showWorkingDayByDate($companyId, $startTimeWorkDay);

            //Neu la cuoi tuan
            if (!$workingDay) {
                continue;
            }

            switch ($diffDay) {
                case 0:
                    $endAt = $this->convertDateTimeToTZ($startDaysStr . ' ' . $workingDay->end_time, $setting->time_zone)->toDateTimeString();
                    break;
                case $diff->days:
                    $startAt = $this->convertDateTimeToTZ($startDaysStr . ' ' . $workingDay->start_time, $setting->time_zone)->toDateTimeString();
                    break;
                default:
                    $startAt = $this->convertDateTimeToTZ($startDaysStr . ' ' . $workingDay->start_time, $setting->time_zone)->toDateTimeString();
                    $endAt = $this->convertDateTimeToTZ($startDaysStr . ' ' . $workingDay->end_time, $setting->time_zone)->toDateTimeString();
            }
            // TODO Neu kiem tra la ngay cuoi tuan hoac ngay nghi le se khong chay vao ham nay
            $data = $this->convertData($startAt, $endAt, $data, $setting);
        }

        return $data;
    }

    /**
     * @param $a
     * @param $b
     * @return int
     */
    function compareTwoDateTime($a, $b): int
    {
        if ($a['date_time'] == $b['date_time']) {
            return 0;
        }

        return ($a['date_time'] < $b['date_time']) ? -1 : 1;
    }

    /**
     * @param $data
     * @param $forms
     * @param $dataIsTimesheet
     * @return array
     */
    function getDateTimeFromForms($data, $forms, $dataIsTimesheet): array
    {
        $dateTimes = [];
        if (($data['start_time'] && $data['end_time']) || ($data['start_time'] && !$data['end_time'])) {
            $dateTimes[] = [
                'date_time' => $data['start_time'],
                'priority' => $dataIsTimesheet ? self::$PRIORITY['TIMESHEET'] : 0
            ];

            $dateTimes[] = [
                'date_time' => $data['end_time'] ?? $data['start_time'],
                'priority' => $dataIsTimesheet ? self::$PRIORITY['TIMESHEET'] : 0
            ];
        }

        foreach ($forms as $index => $form) {
            if (!$form) {
                continue;
            }

            if (($form['start_time'] && $form['end_time']) || ($form['start_time'] && !$form['end_time'])) {
                $dateTimes[] = [
                    'date_time' => $form['start_time'],
                    'priority' => $index + 1
                ];

                $dateTimes[] = [
                    'date_time' => $form['end_time'] ?? $form['start_time'],
                    'priority' => $index + 1
                ];
            }
        }
        return $dateTimes;
    }

    /**
     * @param $timesheet
     * @param $leaveForm
     * @param $compensatoryLeave
     * @return array
     */
    public function timeToCalculate($dateTimes): array // params: $timesheet, $form_1, $form_2
    {
        // sắp xếp thời gian tăng dần
        uasort($dateTimes, array($this, "compareTwoDateTime"));

        $priorityPrevious = [];
        $dateTimesToCalculate = [];

        $period = 0;
        $isAfterTimesheet = false;

        foreach ($dateTimes as $dateTime) {
            $priority = $dateTime['priority'];
            $dateTime = $dateTime['date_time'];
            if (count($priorityPrevious) == 0) {
                $dateTimesToCalculate[$period] = ['start_time' => $dateTime];
            }

            // Kiểm tra đã tồn tại cùng loại $period
            $priorityPrevious[$priority] = isset($priorityPrevious[$priority]) ? ($priorityPrevious[$priority] + 1) : 1;

            if ($this->checkDateTimeTypesAreFullTime($priorityPrevious)) {
                $dateTimesToCalculate[$period]['end_time'] = $dateTime;
                $dateTimesToCalculate[$period]['after_timesheet'] = $isAfterTimesheet;
                if (isset($priorityPrevious[self::$PRIORITY['TIMESHEET']])) {
                    $dateTimesToCalculate[$period]['has_timesheet'] = true;
                    $isAfterTimesheet = true;
                } else {
                    $dateTimesToCalculate[$period]['has_timesheet'] = false;
                }
                ++$period;
                $priorityPrevious = [];
            }
        }

        return $dateTimesToCalculate;
    }

    /**
     * @param $priorityPrevious
     * @return bool
     */
    function checkDateTimeTypesAreFullTime($priorityPrevious): bool
    {
        $isFull = true;
        foreach ($priorityPrevious as $item) {
            if ($item < 2) {
                $isFull = false;
            }
        }
        return $isFull;
    }

    /**
     * @param $data
     * @param $times
     * @param $isAfterTimesheet
     * @param $hasTimesheet
     * @return mixed
     */
    function handleTimeAfterCalculate($data, $times, $isAfterTimesheet, $hasTimesheet, $isEndTimeNull)
    {
        $data['total_time_work'] += $times['total_time_work'];

        // Nếu là khoảng thời gian chứa timesheet
        if ($hasTimesheet) {
            $data['late_time'] += $times['late_time'];

            if (!$isEndTimeNull) {
                $data['time_early'] += $times['time_early'];
            }
            return $data;
        }

        // Thời gian trước TS: trừ đi thời gian đi muộn
        if (!$isAfterTimesheet) {
            $data['late_time'] -= $times['total_time_work'];
        } else { // Thời gian sau TS: trừ đi thời gian về sớm
            $data['time_early'] -= $times['total_time_work'];
        }

        return $data;
    }

    /**
     * @param $startTime
     * @param $endTime
     * @param $times
     * @return bool
     */
    function validateTime($startTime, $endTime, $times)
    {
        foreach ($times as $time) {
            // TODO: Nếu startTime hoặc endTime thuộc khoảng của đơn trước ==> đơn không thỏa mãn
            if (($time['start_time'] < $startTime && $time['end_time'] > $startTime) ||
                ($time['start_time'] < $endTime && $time['end_time'] > $endTime)
            ) {
                return false;
            }
            // TODO: Check khoảng thời gian mới không thuộc các khoảng cũ
            if (!(($time['start_time'] <= $startTime && $time['end_time'] <= $startTime) ||
                ($time['start_time'] >= $endTime && $time['end_time'] >= $endTime))) {
                return false;
            }
        }

        return true;
    }

    function checkInRangeTime($startTime, $endTime, $time)
    {
        $companyId = Auth::user()->company_id;
        $setting = $this->companyService->getSettingOfCompany($companyId);
        $timezone = $setting->time_zone;

        $startTime = Carbon::parse($startTime)->format('Y-m-d H:i:s');
        $endTime = Carbon::parse($endTime)->format('Y-m-d H:i:s');
        $time = $this->convertDateTimeToTZ($time, 'UTC', $timezone)->format('Y-m-d H:i:s');

        if ($time >= $startTime && $time <= $endTime) {
            return true;
        }

        return false;
    }

    function checkRangeTime($startTime, $endTime, $time)
    {
        $companyId = Auth::user()->company_id;
        $setting = $this->companyService->getSettingOfCompany($companyId);
        $timezone = $setting->time_zone;

        $startTime = Carbon::parse($startTime)->format('Y-m-d H:i:s');
        $endTime = Carbon::parse($endTime)->format('Y-m-d H:i:s');
        $time = $this->convertDateTimeToTZ($time, 'UTC', $timezone)->format('Y-m-d H:i:s');

        if ($time > $startTime && $time < $endTime) {
            return true;
        }

        return false;
    }
}
