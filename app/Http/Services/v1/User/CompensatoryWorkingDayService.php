<?php

namespace App\Http\Services\v1\User;

use App\Http\Services\v1\BaseService;
use App\Models\Master\CompensatoryWorkingDay;
use App\Transformers\CompensatoryWorkingDayTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class CompensatoryWorkingDayService extends BaseService
{
    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new CompensatoryWorkingDay();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function setTransformers($data)
    {
        $collection = $data->getCollection();
        $compensatoryWorkingDays = collect($collection)->transformWith(new CompensatoryWorkingDayTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));

        return $compensatoryWorkingDays;
    }
}
