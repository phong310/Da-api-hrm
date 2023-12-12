<?php

namespace App\Http\Services\v1\User;

use App\Http\Services\v1\BaseService;
use App\Models\Master\Holiday;
use App\Transformers\HolidayTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class HolidayService extends BaseService
{
    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new Holiday();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function setTransformers($data)
    {
        $collection = $data->getCollection();
        $holidays = collect($collection)->transformWith(new HolidayTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));

        return $holidays;
    }
}
