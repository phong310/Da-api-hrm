<?php

namespace App\Http\Services\v1\Admin;

use App\Http\Services\v1\BaseService;
use App\Models\OtherAllowance;
use App\Models\OtherAmountOfAllowance;
use App\Repositories\Interfaces\OtherAllowanceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Transformers\OtherAllowanceTransformer;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;


class OtherAllowanceService extends BaseService
{
    /**
     * @var OtherAllowanceInterface
     */
    protected $other_allowance;

    public function __construct(OtherAllowanceInterface $other_allowance)
    {
        $this->other_allowance = $other_allowance;
        parent::__construct();
    }

    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new OtherAllowance();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function setTransformers($data)
    {
        $collection = $data->getCollection();
        return collect($collection)->transformWith(new OtherAllowanceTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));
    }

    public function store(Request $request)
    {
        $name = $request->input('name');
        $companyId = Auth::user()->company_id;
        $data = [
            'company_id' => $companyId,
            'name' => $name,
        ];

        try {
            return $this->other_allowance->store($data);
        } catch (\Exception $e) {
            Log::error($e);
            return false;
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();

        try {
            $allowance = $this->other_allowance->show($id);
            if ($allowance) {
                $companyId = Auth::user()->company_id;
                $updatedData = [
                    'company_id' => $companyId,
                    'name' => $data['name'],
                    'id' => $id
                ];

                return $this->other_allowance->update($allowance, $updatedData);
            }
            return false;
        } catch (\Exception $e) {
            Log::error($e);
            return false;
        }
    }
    public function destroy(Request $request, $id, $isForceDelete = false)
    {
        $companyId = $this->getCompanyId();
        $otherAllowanceExist = OtherAmountOfAllowance::where('other_allowance_id', $id)
            ->whereHas('otherAllowance', function ($q) use ($companyId) {
                $q->where('company_id', $companyId);
            })
            ->exists();
        if ($otherAllowanceExist) {
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
