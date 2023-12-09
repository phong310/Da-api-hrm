<?php

namespace App\Http\Services\v1\Admin;

use App\Exports\BaseExport;
use App\Exports\WorkingDayExportTemplate;
use App\Http\Services\v1\BaseService;
use App\Imports\WorkingDayImport;
use App\Models\Master\WorkingDay;
use App\Repositories\Interfaces\WorkingDayInterface;
use App\Transformers\WorkingDayTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Maatwebsite\Excel\Facades\Excel;

class WorkingDayService extends BaseService
{
    protected $workingDay;

    /**
     * Instantiate a new controller instance.
     *
     * @param WorkingDayInterface $workingDay
     */
    public function __construct(WorkingDayInterface $workingDay)
    {
        $this->workingDay = $workingDay;
        parent::__construct();
    }

    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new WorkingDay();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function setTransformers($data)
    {
        $collection = $data->getCollection();
        $workingDays = collect($collection)->transformWith(new WorkingDayTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));

        return $workingDays;
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function _update(Request $request, $id)
    {
        $data = $request->only($this->model->getFillable());
        $workingDay = $this->workingDay->show($id);

        try {
            if (!$workingDay) {
                return response()->json([
                    'message' => __('message.not_found'),
                ], 404);
            }
            $workingDay->fill($data);
            $workingDay->save();
            $this->updateTotalWorkTime($workingDay);

            return response()->json([
                'message' => __('message.update_success'),
                'data' => $workingDay,
            ]);
        } catch (\Exception $e) {
            Log::error($e);

            return $this->errorResponse();
        }
    }

    /**
     * @param Request $request
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function _store(Request $request, $message = '')
    {
        $data = $request->only($this->model->getFillable());

        try {
            DB::beginTransaction();
            $workingDay = $this->workingDay->store($data);
            $this->updateTotalWorkTime($workingDay);

            DB::commit();

            return response()->json([
                'message' => __('message.created_success'),
                'data' => $workingDay,
                'status' => Response::HTTP_OK,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json(['error' => 'server_error'], 500);
        }
    }

    /**
     * @param Request $request
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function _storeMulti(Request $request, $message = '')
    {
        $data = $request->only($this->model->getFillable());

        try {
            DB::beginTransaction();
            $workingDay = $this->workingDay->stores($data);
            DB::commit();

            return response()->json([
                'message' => __('message.created_success'),
                'data' => $workingDay,
                'status' => Response::HTTP_OK,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json(['error' => 'hello'], 500);
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
        $instance = $this->query->findOrFail($id);
        $instance->delete();

        return response()->json([
            'message' => __('message.delete_success'),
        ]);
    }

    public function getWorkingDayConfig($companyId, $date)
    {
        return $this->workingDay->showWorkingDayByDate($companyId, $date);
    }

    private function updateTotalWorkTime($wd)
    {
        $totalTime = Carbon::parse($wd->start_time)->floatDiffInMinutes(Carbon::parse($wd->end_time));

        if ($wd->start_lunch_break && $wd->end_lunch_break) {
            $totalTime -= Carbon::parse($wd->start_lunch_break)->floatDiffInMinutes(Carbon::parse($wd->end_lunch_break));
        }

        if ($totalTime > 0) {
            $wd->total_working_time = $totalTime;
            $wd->save();
        }
    }
}
