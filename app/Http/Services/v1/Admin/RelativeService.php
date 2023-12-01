<?php

namespace App\Http\Services\v1\Admin;

use App\Http\Services\v1\BaseService;
use App\Models\Relative;
use App\Transformers\EmployeeRelativeTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;


class RelativeService extends BaseService
{
    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new Relative();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function setTransformers($data)
    {
        $collection = $data->getCollection();
        $instances = collect($collection)->transformWith(new EmployeeRelativeTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));

        return $instances;
    }



    /**
     * @param $employee_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByEmployee($employee_id)
    {
        $relatives_fullname = $this->request->get('full_name');

        if ($relatives_fullname  !== null && $relatives_fullname !== '') {
            $this->query->where(function ($query) use ($relatives_fullname) {
                $query->where('first_name', 'LIKE', '%' . $relatives_fullname . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $relatives_fullname . '%')
                    ->orWhereRaw('CONCAT(first_name, " ", last_name) LIKE ?', ['%' . $relatives_fullname . '%']);
            });
        }

        $relatives = $this->query->where(['employee_id' => $employee_id])->get();
        $relatives = $this->addDefaultFilter();
        if (method_exists($this, 'setTransformers')) {
            $data = $this->setTransformers($relatives);
        }

        return response()->json($data);
    }


    public function updateByEmployee(Request $request, $relative_id)
    {
        $data = $request->all();
        $relativesData = $this->query->where(['id' => $relative_id])->first();
        try {
            if (!$relativesData) {
                return response()->json([
                    'message' => __('message.not_found'),
                ], 404);
            }
            $relativesData->update($data);
            return response()->json([
                'message' => __('message.update_success'),
            ]);
        } catch (\Throwable $th) {
            return $this->service->errorResponse();
        }
    }

    public function createByEmployee(Request $request)
    {
        $data = $request->all();

        try {
            Relative::create($data);

            return response()->json([
                'message' => __('message.created_success'),
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            return $this->service->errorResponse();
        }
    }


    public function deleteByEmployee(Request $request, $employee_id, $relative_id)
    {

        try {
            Relative::find($relative_id)->delete();
            return response()->json([
                'message' => __('message.delete_success'),
            ]);
        } catch (\Throwable $th) {
            return $this->service->errorResponse();
        }
    }
}
