<?php

namespace App\Repositories;

use App\Models\Master\CompensatoryWorkingDay;
use App\Repositories\Interfaces\CompensatoryWorkingDayInterface;

class CompensatoryWorkingDayRepository implements CompensatoryWorkingDayInterface
{
    /**
     * @var CompensatoryWorkingDay
     */
    protected $compensatoryWorkingDay;

    /**
     * @param CompensatoryWorkingDay $compensatoryWorkingDay
     */
    public function __construct(CompensatoryWorkingDay $compensatoryWorkingDay)
    {
        $this->compensatoryWorkingDay = $compensatoryWorkingDay;
    }

    /**
     * @param $data
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed
     */
    public function create($data)
    {
        return $this->compensatoryWorkingDay::query()->create($data);
    }

    /**
     * @param $companyId
     * @param $date
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|object|null
     */
    public function getAnnualByDate($companyId, $date)
    {
        return $this->compensatoryWorkingDay::query()->where(['company_id' => $companyId])
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->whereMonth('start_date', '<=', $date)
            ->whereMonth('end_date', '>=', $date)
            ->where(['type' => CompensatoryWorkingDay::TYPE['ANNUAL']])
            ->first();
    }

    /**
     * @param $companyId
     * @param $date
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|object|null
     */
    public function getSingleUseByDate($companyId, $date)
    {
        return $this->compensatoryWorkingDay::query()
            ->where(['company_id' => $companyId])
            ->whereDate('start_date', '<=', $date)
            ->whereDate('end_date', '>=', $date)
            ->where(['type' => CompensatoryWorkingDay::TYPE['SINGLE_USE']])
            ->first();
    }

    /**
     * @param $companyId
     * @param $date
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|object|null
     */
    public function checkCompensatoryWorkingDayByDate($companyId, $date)
    {
        return $this->compensatoryWorkingDay::query()
            ->where(['company_id' => $companyId])
            ->where(function ($q) use ($date) {
                $q->whereDate('start_date', '<=', $date)
                    ->whereDate('end_date', '>=', $date)
                    ->whereMonth('start_date', '<=', $date)
                    ->whereMonth('end_date', '>=', $date)
                    ->where(['type' => CompensatoryWorkingDay::TYPE['ANNUAL']]);
            })->orWhere(function ($q) use ($date) {
                $q->whereDate('start_date', '<=', $date)
                    ->whereDate('end_date', '>=', $date)
                    ->where(['type' => CompensatoryWorkingDay::TYPE['SINGLE_USE']]);
            })->first();
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|object|null
     */
    public function show($id)
    {
        return $this->compensatoryWorkingDay::query()->where(['id' => $id])->first();
    }
}
