<?php

namespace App\Http\Services\v1\Admin;

use App\Http\Services\v1\BaseService;
use App\Transformers\BaseMasterTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class BaseMasterService extends BaseService
{
    /**
     * @return mixed
     */
    protected function setModel()
    {
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $message = '')
    {
        $data = [
            'name' => $request->name,
        ];
        try {
            DB::beginTransaction();
            $instance = $this->query->create($data);
            $instance->save();
            DB::commit();

            return response()->json([
                'message' => __('message.created_success'),
            ]);
        } catch (\Exception $e) {
            //throw $th;
            DB::rollBack();
            Log::error($e);

            return $this->service->errorResponse();
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $data = $request->only($this->model->getFillable());
        $instance = $this->query->where('id', $id)->first();

        try {
            DB::beginTransaction();
            $instance->fill($data);
            $instance->save();
            DB::commit();

            return response()->json([
                'message' => __('message.update_success'),
                'data' => $instance,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return $this->service->errorResponse();
        }
    }

    /**
     * @param $data
     * @return mixed
     */
    public function setTransformers($data)
    {
        $collection = $data->getCollection();
        $instances = collect($collection)->transformWith(new BaseMasterTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));

        return $instances;
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Http\JsonResponse|mixed
     */
    public function show(Request $request, $id)
    {
        $instance = $this->query->find($id);
        if (!$instance) {
            return response()->json([
                'message' => __('message.not_found'),
            ], 404);
        }

        return response()->json(fractal($instance, new BaseMasterTransformer()));
    }

    /**
     * @param Request $request
     * @param $id
     * @param false $isForceDelete
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id, $isForceDelete = false)
    {
        $instance = $this->query->findOrFail($id);
        $instance->delete();

        return response()->json([
            'message' => __('message.deleted_success'),
        ]);
    }
}
