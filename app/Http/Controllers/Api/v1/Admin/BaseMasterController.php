<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseMasterController extends Controller
{
    /**
     * @var
     */
    protected $service;

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        return $this->service->index($request);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function all(Request $request)
    {
        return $this->service->all($request);
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function show(Request $request, $id)
    {
        return $this->service->show($request, $id);
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function destroy(Request $request, $id)
    {
        return $this->service->destroy($request, $id, true);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function export(Request $request)
    {
        return $this->service->export($request);
    }

    /**
     * @return mixed
     */
    public function exportTemplate()
    {
        return $this->service->exportTemplate();
    }

    /**
     * @return mixed
     */
    public function import()
    {
        return $this->service->import();
    }
}
