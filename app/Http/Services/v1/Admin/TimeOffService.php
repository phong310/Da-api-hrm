<?php

namespace App\Http\Services\v1\Admin;

use App\Http\Services\v1\BaseService;
use App\Models\Form\NumberOfDaysOff;
use App\Transformers\NumberOfDaysOffTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class TimeOffService extends BaseService
{
    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new NumberOfDaysOff();
    }

    public function appendFilter()
    {

        $today = Carbon::now();
        $year = $today->year;
        $month = $today->month;
        if ($this->request->get('month')) {
            $yearMonth = explode('-', $this->request->get('month'));
            $month = $yearMonth[1];
            $year = $yearMonth[0];
        }

        $this->query
            ->whereYear('date', '=', $year)
            ->whereMonth('date', '=', $month);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function setTransformers($data)
    {
        $collection = $data->getCollection();
        $instances = collect($collection)->transformWith(new NumberOfDaysOffTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));

        return $instances;
    }

    /**
     * @param $employee_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByEmployee($employee_id)
    {
        $timeOff = $this->query
            ->whereHas('employee', function ($q) use ($employee_id) {
                $q->where(['employees.id' => $employee_id]);
            })->paginate();

        if (method_exists($this, 'setTransformers')) {
            $data = $this->setTransformers($timeOff);
        }

        return response()->json($data);
    }

    /**
     * @param Request $request
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $message = '')
    {
        $numberDaysOff = NumberOfDaysOff::create([
            'employee_id' => $request->employee_id,
            'date' => $request->date,
            'type' => $request->type,
            'number_of_minutes' => intval($request->number_of_minutes),
        ]);

        return response()->json(['message' => __('message.created_success')]);
    }

    /**
     * @param Request $request
     * @param $leaveForm
     * @return mixed
     */
    public function update(Request $request, $leaveForm)
    {
        $numberDaysOff = $leaveForm->number_of_minutes_off->update([
            'date' => $this->parseDate($request->approval_deadline),
            'number_of_minutes' => $request->number_leave_day,
        ]);

        return $numberDaysOff;
    }

    /**
     * @param Request $request
     * @param $employee_id
     * @param $timeOffId
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteByEmployee(Request $request, $employee_id, $timeOffId)
    {
        try {
            NumberOfDaysOff::find($timeOffId)->delete();

            return response()->json([
                'message' => __('message.update_success'),
            ]);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'server_error'], 500);
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

        return response()->json(['message' => __('message.delete_success')]);
    }
}
