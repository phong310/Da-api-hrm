<?php

namespace App\Http\Services\v1\Admin;

use App\Http\Services\v1\BaseService;
use App\Models\Master\CompensatoryWorkingDay;
use App\Models\Master\WorkingDay;
use App\Repositories\Interfaces\CompensatoryWorkingDayInterface;
use App\Transformers\CompensatoryWorkingDayTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class CompensatoryWorkingDayService extends BaseService
{
    /**
     * @var CompensatoryWorkingDayInterface
     */
    protected $compensatoryWorkingDay;

    /**
     * @param CompensatoryWorkingDayInterface $compensatoryWorkingDay
     */
    public function __construct(CompensatoryWorkingDayInterface $compensatoryWorkingDay)
    {
        $this->compensatoryWorkingDay = $compensatoryWorkingDay;
        parent::__construct();
    }

    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new CompensatoryWorkingDay();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function setTransformers($data)
    {
        $collection = $data->getCollection();
        $compensatoryWorkingDays = collect($collection)->transformWith(new CompensatoryWorkingDayTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));

        return $compensatoryWorkingDays;
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
        $companyId = Auth::user()->company_id;
        $start_date = Carbon::parse($request->start_date);
        $end_date = Carbon::parse($request->end_date);
        while ($start_date <= $end_date) {
            $code_day = $start_date->dayOfWeek;
            $wd = WorkingDay::query()->where([
                'company_id' => $companyId,
                'day_in_week_id' => $code_day,
            ])->first();
            if ($wd) {
                return response()->json([
                    'message' => __('message.not_compensatory_workingDay'),
                ], 403);
            }
            $start_date->addDay();
        }
        try {
            $data = $request->only($this->model->getFillable());
            $result = $this->compensatoryWorkingDay->create($data);
            return $this->createResultResponse($result, $message);
            $this->_store($request);
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
        $companyId = Auth::user()->company_id;
        $start_date = Carbon::parse($request->start_date);
        $end_date = Carbon::parse($request->end_date);
        $compensatoryWorkingDay = $this->compensatoryWorkingDay->show($id);
        if (!$compensatoryWorkingDay) {
            return response()->json([
                'message' => __('message.not_found'),
            ], 404);
        }
        while ($start_date <= $end_date) {
            $code_day = $start_date->dayOfWeek;
            $wd = WorkingDay::query()->where([
                'company_id' => $companyId,
                'day_in_week_id' => $code_day,
            ])->first();
            if ($wd) {
                return response()->json([
                    'message' => __('message.not_compensatory_workingDay'),
                ], 403);
            }
            $start_date->addDay();
        }
        try {
            $data = $request->only($this->model->getFillable());
            $compensatoryWorkingDay->fill($data);
            $compensatoryWorkingDay->save();

            return response()->json([
                'message' => __('message.update_success'),
                'data' => $compensatoryWorkingDay,
            ]);
        } catch (\Exception $e) {
            Log::error($e);

            return $this->errorResponse();
        }
    }

    /**
     * @param $companyId
     * @param $date
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function checkCompensatoryWorkingDayOfCompany($companyId, $date)
    {
        $date = Carbon::parse($date)->format('Y-m-d');

        return $this->compensatoryWorkingDay->checkCompensatoryWorkingDayByDate($companyId, $date);
    }

    public function destroy(Request $request, $id, $isForceDelete = false)
    {
        $instance = $this->query->findOrFail($id);
        $instance->delete();

        return response()->json([
            'message' => __('message.delete_success'),
        ]);
    }
}
