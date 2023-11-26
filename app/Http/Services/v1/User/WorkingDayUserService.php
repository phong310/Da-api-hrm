<?php

namespace App\Http\Services\v1\User;

use App\Exports\BaseExport;
use App\Exports\WorkingDayExportTemplate;
use App\Http\Services\v1\BaseService;
use App\Imports\WorkingDayImport;
use App\Models\Master\WorkingDay;
use App\Transformers\WorkingDayTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Maatwebsite\Excel\Facades\Excel;

class WorkingDayUserService extends BaseService
{
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

    public function getByCompanyId($companyId)
    {
        return $this->query->where('company_id', $companyId)->get();
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

            // loop
            foreach ($data['name'] as $key => $value) {
                $newValue = [];
                $newValue['name'] = $value;
                $newValue['day_in_week_id'] = $data['day_in_week_id'][$key];
                $newValue['type'] = $data['type'];
                $newValue['start_time'] = $data['start_time'];
                $newValue['end_time'] = $data['end_time'];
                $newValue['start_lunch_break'] = $data['start_lunch_break'];
                $newValue['end_lunch_break'] = $data['end_lunch_break'];
                $newValue['company_id'] = $data['company_id'];

                $workingDay = WorkingDay::create($newValue);
            }
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

    public function updateByCompanyId($data, $companyId)
    {
        try {
            DB::beginTransaction();

            $oldData = $this->getByCompanyId($companyId);
            return $oldData;

            foreach ($oldData as $item) {
                if (!in_array($item->day_in_week_id, $data['day_in_week_id'])) {
                    $item->forceDelete();
                }
            }

            foreach ($data['name'] as $key => $value) {
                $dataCompare['name'] = $value;
                $dataCompare['day_in_week_id'] = $data['day_in_week_id'][$key];
                $dataCompare['company_id'] = $companyId;

                $newValue = [
                    'type' => $data['type'],
                    'start_time' => $data['start_time'],
                    'end_time' => $data['end_time'],
                    'start_lunch_break' => $data['start_lunch_break'],
                    'end_lunch_break' => $data['end_lunch_break']
                ];

                $workingDay = WorkingDay::updateOrCreate($dataCompare, array_merge($dataCompare, $newValue));
            }

            DB::commit();

            return response()->json([
                'message' => __('message.update_success'),
                'data' => $workingDay,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // /**
    //  * @param Request $request
    //  * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
    //  */
    // public function export(Request $request)
    // {
    //     return Excel::download(new BaseExport(new WorkingDay(), ['id', 'name', 'type', 'start_time', 'end_time', 'day_in_week_id'], $request), 'holidays.xlsx');
    // }

    // /**
    //  * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
    //  */
    // public function exportTemplate()
    // {
    //     return Excel::download(new WorkingDayExportTemplate(), 'working_days.xlsx');
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
    //     Excel::import(new WorkingDayImport(), request()->file('file'));
    //     // Log::error($import->errors());
    //     return response()->json([
    //         'message' => 'Import success',
    //     ], 200);
    // }
}
