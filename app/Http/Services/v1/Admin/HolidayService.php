<?php

namespace App\Http\Services\v1\Admin;

use App\Exports\BaseExport;
use App\Exports\HolidayExportTemplate;
use App\Http\Services\v1\BaseService;
use App\Imports\HolidayImport;
use App\Repositories\Interfaces\HolidayInterface;
use App\Transformers\HolidayTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Master\Holiday;

class HolidayService extends BaseService
{
    protected $holiday;

    /**
     * Instantiate a new controller instance.
     *
     * @param HolidayInterface $holiday
     */
    public function __construct(HolidayInterface $holiday)
    {
        $this->holiday = $holiday;
        parent::__construct();
    }

    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new Holiday();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function setTransformers($data)
    {
        $collection = $data->getCollection();
        $holidays = collect($collection)->transformWith(new HolidayTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));
        return $holidays;
    }
    public function appendFilter()
    {
        $this->query->where(['type' => $this->request->type]);
    }

    /**
     * @param Request $request
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $message = '')
    {
        $data = $request->only($this->model->getFillable());
        try {
            $result = $this->holiday->create($data);

            return $this->createResultResponse($result, $message);
        } catch (\Exception $e) {
            Log::error($e);

            return $this->errorResponse();
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $holiday = $this->holiday->show($id);

        if (!$holiday) {
            return response()->json([
                'message' => __('message.not_found'),
            ], 404);
        }

        try {
            $data = $request->only($this->model->getFillable());
            $holiday->fill($data);
            $holiday->save();

            return response()->json([
                'message' => __('message.update_success'),
                'data' => $holiday,
            ]);
        } catch (\Exception $e) {
            Log::error($e);

            return $this->errorResponse();
        }
    }

    // /**
    //  * @param Request $request
    //  * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
    //  */
    // public function export(Request $request)
    // {
    //     return Excel::download(new BaseExport(new Holiday(), ['id', 'name', 'date'], $request), 'holidays.xlsx');
    // }

    // /**
    //  * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
    //  */
    // public function exportTemplate()
    // {
    //     return Excel::download(new HolidayExportTemplate(), 'holidays.xlsx');
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
            'message' => __('message.delete_success'),
        ]);
    }

    // /**
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function import()
    // {
    //     Excel::import(new HolidayImport(), request()->file('file'));

    //     return response()->json([
    //         'message' => 'Import success',
    //     ], 200);
    // }

    /**
     * @param $companyId
     * @param $date
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function checkHolidayOfCompany($companyId, $date)
    {
        $date = Carbon::createFromFormat('Y-m-d', $date);

        return $this->holiday->checkHolidayByDate($companyId, $date);
    }
}
