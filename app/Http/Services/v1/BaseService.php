<?php

namespace App\Http\Services\v1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

abstract class BaseService
{
    /** @var Model */
    protected $model;

    /** @var Builder */
    protected $query;

    /** @var Request */
    protected $request;

    public function __construct()
    {
        $this->request = request();
        $this->setModel();
        $this->setQuery();
    }

    /**
     * @return mixed
     */
    abstract protected function setModel();

    protected function setQuery()
    {
        $this->query = $this->model->query();
    }

    public function appendCompanyFilter()
    {
        $user = Auth::user();
        $company_id = '';
        $fillable = $this->model->getFillable();

        if (!in_array('company_id', $fillable)) {
            return;
        }

        if ($user && $user->company_id) {
            $company_id = $user->company_id;
        } else {
            $companyIdHeader = request()->header('companyId');
            if ($companyIdHeader) {
                $company_id = $companyIdHeader;
            }
        }
        if ($company_id) {
            $this->query->where(['company_id' => $company_id]);
        }
    }

    public function getCompanyId()
    {
        $user = Auth::user();
        $company_id = 1;

        if ($user && $user->company_id) {
            $company_id = $user->company_id;
        } else {
            $companyIdHeader = request()->header('companyId');
            if ($companyIdHeader) {
                $company_id = $companyIdHeader;
            }
        }

        return $company_id;
    }

    public function addCompanyToRequest()
    {
        $user = Auth::user();
        $company_id = '';
        $fillable = $this->model->getFillable();

        if (!in_array('company_id', $fillable)) {
            return;
        }

        if ($user && $user->company_id) {
            $company_id = $user->company_id;
        } else {
            $companyIdHeader = request()->header('companyId');
            if ($companyIdHeader) {
                $company_id = $companyIdHeader;
            }
        }
        if ($company_id) {
            $this->request->merge(['company_id' => $company_id]);
        }
    }

    protected function addDefaultFilter()
    {
        $data = $this->request->all();
        $table = $this->model->getTable();
        $fields = ['*'];

        foreach ($data as $key => $value) {
            if ($value || $value === '0') {
                try {
                    if (strpos($key, ':') !== false) {
                        $field = str_replace(':', '.', $key);
                        $query = $this->query;
                        if (preg_match('/(.*)_like$/', $field, $matches)) {
                            $relations = explode('.', $matches[1]);
                            if (count($relations) == 3) {
                                $query->whereHas(
                                    $relations[0],
                                    function ($query) use ($relations, $value) {
                                        $query->whereHas($relations[1], function ($query) use ($relations, $value) {
                                            $query->where($relations[2], 'like', "%$value%");
                                        });
                                    }
                                );
                            } else {
                                $query->whereHas(
                                    $relations[0],
                                    function ($query) use ($relations, $value) {
                                        $query->where($relations[1], 'like', "%$value%");
                                    }
                                );
                            }
                        }

                        if (preg_match('/(.*)_equal$/', $field, $matches)) {
                            $relations = explode('.', $matches[1]);
                            if (count($relations) == 3) {
                                $query->whereHas(
                                    $relations[0],
                                    function ($query) use ($relations, $value) {
                                        $query->whereHas($relations[1], function ($query) use ($relations, $value) {
                                            $query->where($relations[2], '=', $value);
                                        });
                                    }
                                );
                            } else {
                                $query->whereHas(
                                    $relations[0],
                                    function ($query) use ($relations, $value) {
                                        $query->where($relations[1], '=', $value);
                                    }
                                );
                            }
                            // $query->whereHas($relations[0], function ($query) use ($relations, $value) {
                            //     $query->where($relations[1], '=', $value);
                            // });
                        }
                    } else {
                        if (preg_match('/(.*)_like$/', $key, $matches)) {
                            if (config('database.default') === 'sqlsrv') {
                                //                                $value = $this->convert_vi_to_en($value);
                                $this->query->where($table . '.' . $matches[1], 'like', "%$value%");
                            } else {
                                $this->query->where($table . '.' . $matches[1], 'like', '%' . $value . '%');
                            }
                        }
                        if (preg_match('/(.*)_equal$/', $key, $matches)) {
                            $value = explode(',', $value);
                            if (sizeof($value) === 1) {
                                $this->query->where($table . '.' . $matches[1], $value);
                            } else {
                                $this->query->whereIn($table . '.' . $matches[1], $value);
                            }
                        }
                        if (preg_match('/(.*)_notEqual$/', $key, $matches)) {
                            $value = explode(',', $value);
                            if (sizeof($value) === 1) {
                                $this->query->where($table . '.' . $matches[1], '!=', $value);
                            } else {
                                $this->query->whereNotIn($table . '.' . $matches[1], $value);
                            }
                        }
                        if (preg_match('/(.*)_between$/', $key, $matches)) {
                            $this->query->whereBetween($table . '.' . $matches[1], $value);
                        }
                        if (preg_match('/(.*)_isnull$/', $key, $matches)) {
                            if ($value == 1) {
                                $this->query->whereNull($table . '.' . $matches[1]);
                            }
                            if ($value == 0) {
                                $this->query->whereNotNull($table . '.' . $matches[1]);
                            }
                        }
                    }
                    if (preg_match('/^has_(.*)/', $key, $matches)) {
                        if ($value) {
                            $this->query->whereHas($matches[1]);
                        } else {
                            $this->query->whereDoesntHave($matches[1]);
                        }
                    }
                    if ($key == 'only_trashed' && $value) {
                        $this->query->onlyTrashed();
                    }
                    if ($key == 'with_trashed' && $value) {
                        $this->query->withTrashed();
                    }

                    if ($key == 'select' && $value) {
                        $this->query->select($value);
                    }

                    if ($key == 'sort' && $value) {
                        $sorts = explode(',', $value);
                        $this->query->getQuery()->orders = null;
                        foreach ($sorts as $sort) {
                            $sortParams = explode('|', $sort);
                            if (strpos($sortParams[0], '.') !== false) {
                                $this->query->orderByJoin($sortParams[0], isset($sortParams[1]) ? $sortParams[1] : 'asc');
                            } else {
                                $this->query->orderBy($table . '.' . $sortParams[0], isset($sortParams[1]) ? $sortParams[1] : 'asc');
                            }
                        }
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        $per_page = $this->request->per_page;

        if ($per_page != -1) {
            return $this->query
                ->orderBy($this->model->getTable() . '.id', 'desc')
                ->paginate($per_page ?: 20);
        }

        return $this->query->get();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if (method_exists($this, 'appendFilter')) {
            $this->appendFilter();
        }
        $this->appendCompanyFilter();
        $data = $this->addDefaultFilter();
        if (method_exists($this, 'setTransformers') && $request->per_page != -1) {
            $data = $this->setTransformers($data);
        }

        return response()->json($data);
    }

    /**
     * @param Request $request
     * @param $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function _store(Request $request, $message = '')
    {
        $data = $request->only($this->model->getFillable());

        $result = $this->query->create($data);

        return $this->createResultResponse($result, $message);
    }

    /**
     *      * Update the specified resource in storage.
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function _update(Request $request, $id)
    {
        $data = $request->all();
        if (isset($data['id'])) {
            unset($data['id']);
        }
        $item = $this->query->where('id', $id)->first();
        if (!$item) {
            return response()->json(null, 404);
        }
        $item->fill($data);

        $result = $item->update();

        return $this->createResultResponse($result);
    }

    /**
     * Display the specified resource.
     * @param Request $request
     * @param $id
     * @return Builder|mixed
     */
    public function show(Request $request, $id)
    {
        if (method_exists($this, 'checkMySelf')) {
            $this->checkMySelf();
        }
        $item = $this->query->find($id);
        if (!$item) {
            return response()->json([
                'message' => __('message.not_found'),
            ], 404);
        }
        if (method_exists($this, 'afterFind')) {
            $item = $this->afterFind($item);
        }

        return response()->json($item);
    }

    public function destroy(Request $request, $id, $isForceDelete = false)
    {

        if ($isForceDelete) {
            $this->_forceDelete($request, $id);
        } else {
            $this->_softDelete($request, $id);
        }

        return response()->json(['message' => __('message.delete_success')]);
    }

    private function _softDelete(Request $request, $id)
    {
        // create Observer to handle cascade soft deletion
        $model = $this->query->findOrFail($id);
        $model->delete();
    }

    private function _forceDelete(Request $request, $id)
    {
        $model = $this->query->withTrashed()->findOrFail($id);
        $model->forceDelete();
    }

    public function restore(Request $request, $id)
    {
        // create Observer to handle cascade soft restoration
        $model = $this->query->onlyTrashed()->findOrFail($id);
        $model->restore();

        return response()->json(['message' => __('message.restore_success')]);
    }

    /**
     * @param $data
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createResultResponse($data, $message = '')
    {
        return $this->successResponse(['message' => $message ?: __('message.created_success'), 'data' => $data]);
    }

    /**
     * @param null $responseData
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($responseData = null)
    {
        return response()->json($responseData);
    }

    public function errorResponse($message = '')
    {
        return response()
            ->json([
                'message' => __('message.server_error'),
            ], 500);
    }
}
