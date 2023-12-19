<?php

namespace App\Http\Services\v1\User\LaborContract;

use App\Http\Services\v1\BaseService;
use App\Models\LaborContract\LaborContractType;
use App\Repositories\Interfaces\LaborContract\LaborContractTypeInterface;
use App\Transformers\EmployeeTransformer;
use App\Transformers\LaborContract\LaborContractTypeTransformer;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class LaborContractTypeService extends BaseService
{
    /**
     * @var LaborContractTypeInterface
     */
    protected $laborContractType;

    public function __construct(LaborContractTypeInterface $laborContractType)
    {
        $this->laborContractType = $laborContractType;

        parent::__construct();
    }

    public function setModel()
    {
        $this->model = new LaborContractType();
    }

    public function setTransformers($data)
    {
        $collection = $data->getCollection();
        $instances = collect($collection)->transformWith(new LaborContractTypeTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));

        return $instances;
    }

    public function appendFilter()
    {
        $this->query->with(['allowances']);
    }
}
