<?php

namespace App\Repositories;

use App\Models\Master\Holiday;
use App\Repositories\Interfaces\HolidayInterface;

class HolidayRepository implements HolidayInterface
{
    /**
     * @var Holiday
     */
    protected $holiday;

    /**
     * @param Holiday $holiday
     */
    public function __construct(Holiday $holiday)
    {
        $this->holiday = $holiday;
    }

    /**
     * @param $data
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed
     */
    public function create($data)
    {
        return $this->holiday::query()->create($data);
    }

    /**
     * @param $companyId
     * @param $date
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|object|null
     */
    public function getAnnualByDate($companyId, $date)
    {
        return $this->holiday::query()->where(['company_id' => $companyId])
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->whereMonth('start_date', '<=', $date)
            ->whereMonth('end_date', '>=', $date)
            ->where(['type' => Holiday::TYPE['ANNUAL']])
            ->first();
    }

    /**
     * @param $companyId
     * @param $date
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|object|null
     */
    public function getSingleUseByDate($companyId, $date)
    {
        return $this->holiday::query()
            ->where(['company_id' => $companyId])
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->where(['type' => Holiday::TYPE['SINGLE_USE']])
            ->first();
    }

    /**
     * @param $companyId
     * @param $date
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|object|null
     */
    public function checkHolidayByDate($companyId, $date)
    {
        return $this->holiday::query()
            ->where(['company_id' => $companyId])
            ->where(function ($q) use ($date) {
                $q->whereDate('start_date', '<=', $date)
                    ->whereDate('end_date', '>=', $date)
                    ->whereMonth('start_date', '<=', $date)
                    ->whereMonth('end_date', '>=', $date)
                    ->where(['type' => Holiday::TYPE['ANNUAL']]);
            })->orWhere(function ($q) use ($date) {
                $q->whereDate('start_date', '<=', $date)
                    ->whereDate('end_date', '>=', $date)
                    ->where(['type' => Holiday::TYPE['SINGLE_USE']]);
            })->first();
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|object|null
     */
    public function show($id)
    {
        return $this->holiday::query()->where(['id' => $id])->first();
    }
}
