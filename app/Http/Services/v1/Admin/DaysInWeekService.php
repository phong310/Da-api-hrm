<?php

namespace App\Http\Services\v1\Admin;

use App\Exports\BaseExport;
use App\Exports\DaysInWeekExportTemplate;
use App\Http\Services\v1\BaseService;
use App\Imports\DaysInWeekImport;
use App\Models\Master\DaysInWeek;
use App\Transformers\DaysInWeekTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Maatwebsite\Excel\Facades\Excel;

class DaysInWeekService extends BaseService
{
    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new DaysInWeek();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function setTransformers($data)
    {
        $collection = $data->getCollection();
        $diws = collect($collection)->transformWith(new DaysInWeekTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));

        return $diws;
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $data = $request->only($this->model->getFillable());
        $daysInWeek = $this->query->where('id', $id)->first();

        try {
            if (!$daysInWeek) {
                return response()->json([
                    'message' => __('message.not_found'),
                ], 404);
            }
            $daysInWeek->fill($data);
            $daysInWeek->save();

            return response()->json([
                'message' => __('message.update_success'),
                'data' => $daysInWeek,
            ]);
        } catch (\Exception $e) {
            Log::error($e);

            return $this->service->errorResponse();
        }
    }

    // /**
    //  * @param Request $request
    //  * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
    //  */
    // public function export(Request $request)
    // {
    //     return Excel::download(new BaseExport(new DaysInWeek(), ['id', 'name', 'symbol'], $request), 'days_in_week.xlsx');
    // }

    // /**
    //  * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
    //  */
    // public function exportTemplate()
    // {
    //     return Excel::download(new DaysInWeekExportTemplate(), 'days_in_week.xlsx');
    // }

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

    // /**
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function import()
    // {
    //     Excel::import(new DaysInWeekImport(), request()->file('file'));
    //     // Log::error($import->errors());
    //     return response()->json([
    //         'message' => 'Import success',
    //     ], 200);
    // }
}
