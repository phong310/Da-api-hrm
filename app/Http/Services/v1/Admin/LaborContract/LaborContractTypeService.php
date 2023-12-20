<?php

namespace App\Http\Services\v1\Admin\LaborContract;

use App\Http\Services\v1\BaseService;
use App\Models\LaborContract\LaborContract;
use App\Models\LaborContract\LaborContractType;
use App\Repositories\Interfaces\LaborContract\LaborContractTypeInterface;
use App\Transformers\LaborContract\LaborContractTypeTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class LaborContractTypeService extends BaseService
{
    public function __construct(LaborContractTypeInterface $laborContractType)
    {
        $this->laborContractType = $laborContractType;

        parent::__construct();
    }

    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new LaborContractType();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function setTransformers($data)
    {
        $collection = $data->getCollection();
        return collect($collection)->transformWith(new LaborContractTypeTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));
    }

    public function afterFind($data)
    {
        $allowances = Arr::pluck($data->allowances->toArray(), 'id');
        unset($data['allowances']);
        $data['allowances'] = $allowances;
        return $data;
    }

    public function store(Request $request)
    {
        $data = $request->only($this->model->getFillable());
        $companyId = $this->getCompanyId();

        $data['company_id'] = $companyId;

        return $this->laborContractType->store($data);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $data = $request->only($this->model->getFillable());

        return $this->laborContractType->update($data, $id);
    }

    /**
     * @param Request $request
     * @param $id
     * @param false $isForceDelete
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id, $isForceDelete = false)
    {
        $companyId = $this->getCompanyId();
        $contractTypeExist = LaborContract::query()->where('company_id', $companyId)->where('labor_contract_type_id', $id)->exists();

        if ($contractTypeExist) {
            return response()->json([
                'message' => __('message.delete_labor_contract_type_exist'),
            ], 403);
        }

        $laborContractType = $this->query->findOrFail($id);

        if ($isForceDelete) {
            $laborContractType->forceDelete();
        } else {
            $laborContractType->delete();
        }

        return response()->json([
            'message' => __('message.delete_success'),
        ]);
    }
}
