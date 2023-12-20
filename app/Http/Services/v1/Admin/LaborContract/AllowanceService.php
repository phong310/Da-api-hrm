<?php

namespace App\Http\Services\v1\Admin\LaborContract;

use App\Http\Services\v1\BaseService;
use App\Models\LaborContract\Allowance;
use App\Models\LaborContract\LaborContractHasAllowance;
use App\Repositories\Interfaces\LaborContract\AllowanceInterface;
use App\Transformers\AllowanceTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new Allowance();
    }

    //    public function appendFilter()
    //    {
    //        $this->query->where('status', Allowance::STATUS['ACTIVE']);
    //    }

    /**
     * @param $data
     * @return mixed
     */
    public function setTransformers($data)
    {
        $collection = $data->getCollection();
        return collect($collection)->transformWith(new AllowanceTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $data = $request->only($this->model->getFillable());
        $allowance = $this->allowance->show($id);

        try {
            if (!$allowance) {
                return response()->json([
                    'message' => __('message.not_found'),
                ], 404);
            }
            $allowance->fill($data);
            $allowance->save();

            return response()->json([
                'message' => __('message.update_success'),
                'data' => $allowance,
            ]);
        } catch (\Exception $e) {
            Log::error($e);

            return $this->errorResponse();
        }
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
        $allowanceExist = LaborContractHasAllowance::where('allowance_id', $id)
            ->whereHas('allowance', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->exists();



        if ($allowanceExist) {
            return response()->json([
                'message' => __('message.delete_allowance_exist'),
            ], 403);
        }

        $instance = $this->query->findOrFail($id);
        $instance->delete();

        return response()->json([
            'message' => __('message.delete_success'),
        ]);
    }
}
