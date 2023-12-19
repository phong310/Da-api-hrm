<?php

namespace App\Http\Services\v1\User\LaborContract;

use App\Http\Services\v1\BaseService;
use App\Models\LaborContract\Allowance;
use App\Repositories\Interfaces\LaborContract\AllowanceInterface;
use App\Transformers\LaborContract\AllowanceTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class AllowanceService extends BaseService
{
    /**
     * @var AllowanceInterface
     */
    protected $allowance;

    public function __construct(AllowanceInterface $allowance)
    {
        $this->allowance = $allowance;

        parent::__construct();
    }

    public function setModel()
    {
        $this->model = new Allowance();
    }

    public function appendFilter()
    {
        $this->query->where('status', Allowance::STATUS['ACTIVE']);
    }

    public function setTransformers($data)
    {
        $collection = $data->getCollection();
        $instances = collect($collection)->transformWith(new AllowanceTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));

        return $instances;
    }
}
