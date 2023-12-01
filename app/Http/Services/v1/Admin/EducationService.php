<?php

namespace App\Http\Services\v1\Admin;

use App\Http\Services\v1\BaseService;
use App\Models\Education;
use App\Transformers\EmployeeEducationTransformer;
use Illuminate\Http\Request;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class EducationService extends BaseService
{
    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new Education();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = $this->addDefaultFilter();

        return response()->json($data);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function setTransformers($data)
    {
        $collection = $data->getCollection();
        $instances = collect($collection)->transformWith(new EmployeeEducationTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));

        return $instances;
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|mixed|void
     */
    public function show(Request $request, $id)
    {
    }

    /**
     * @param Request $request
     */
    public function store(Request $request)
    {
    }

    /**
     * @param Request $request
     * @param $id
     */
    public function update(Request $request, $id)
    {
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

        return response()->json(['message' => __('message.delete_success')]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createByEmployee(Request $request)
    {
        $data = $request->all();

        try {
            Education::create($data);

            return response()->json([
                'message' => __('message.created_success'),
            ]);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'server_error'], 500);
        }
    }

    /**
     * @param $employee_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByEmployee($employee_id)
    {
        $employee_school_name = $this->request->get('school_name');

        if ($employee_school_name !== null && $employee_school_name !== '') {
            $this->query->where('school_name', 'LIKE', '%' . $employee_school_name . '%');
        }
        $educations = $this->query
            ->whereHas('personalInformation', function ($q) use ($employee_id) {
                $q->whereHas('employee', function ($q) use ($employee_id) {
                    $q->where(['employees.id' => $employee_id]);
                });
            })->get();

        $educations = $this->addDefaultFilter();

        if (method_exists($this, 'setTransformers')) {
            $data = $this->setTransformers($educations);
        }

        return response()->json($data);
    }

    /**
     * @param Request $request
     * @param $employee_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateByEmployee(Request $request, $employee_id)
    {
        $data = $request->all();

        $educationsData = $this->query->whereHas('personalInformation', function ($q) use ($employee_id) {
            $q->whereHas('employee', function ($q) use ($employee_id) {
                $q->where(['employees.id' => $employee_id]);
            });
        })->get();

        try {
            if (!$educationsData) {
                return response()->json([
                    'message' => __('message.not_found'),
                ], 404);
            }

            foreach ($data['educations'] as $education) {
                Education::find($education['id'])->update(collect($education)
                    ->only((new Education())->getFillable())->all());
            }

            return response()->json([
                'message' => __('message.update_success'),
            ]);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'server_error'], 500);
        }
    }

    /**
     * @param Request $request
     * @param $employee_id
     * @param $education_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteByEmployee(Request $request, $employee_id, $education_id)
    {

        try {
            Education::find($education_id)->delete();

            return response()->json([
                'message' => __('message.delete_success'),
            ]);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'server_error'], 500);
        }
    }
}
