<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BaseController extends Controller
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
     * @param $id
     * @return mixed
     */
    public function show(Request $request, $id)
    {
        return $this->service->show($request, $id);
    }

    // public function store(Request $request)
    // {
    //     return $this->service->store($request);
    // }

    // public function update(Request $request, $id)
    // {
    //     return $this->service->update($request, $id);
    // }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function destroy(Request $request, $id)
    {
        return $this->service->destroy($request, $id);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function export(Request $request)
    {
        return $this->service->export($request);
    }
}
