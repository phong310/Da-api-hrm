<?php

namespace App\Http\Services\v1\User;

use App\Exports\BaseExport;
use App\Models\TimeSheet\TimeSheet;
use App\Models\TimeSheet\TimeSheetsLog;
use App\Repositories\Interfaces\EmployeeInterface;
use App\Transformers\TimesheetLogManagerTransform;
use App\Transformers\TimeSheetLogTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Maatwebsite\Excel\Facades\Excel;

class TimeSheetLogService extends UserBaseService
{
    public function __construct(EmployeeInterface $employee)
    {
        $this->employee = $employee;
    }

    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new TimeSheetsLog();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function appendFilter()
    {
        $employee_id = Auth::user()->employee_id;

        if (isset($request->month)) {
            $yearMonth = explode('-', $request->month);
            $month = $yearMonth[1];
            $year = $yearMonth[0];
            $this->query->whereYear('date_time', '=', $year)->whereMonth('date_time', '=', $month);
        }

        $this->query
            ->where('employee_id', $employee_id)
            ->orderBy('date_time', 'DESC');
    }

    /**
     * @param $data
     * @return mixed
     */
    public function setTransformers($data)
    {
        $collection = $data->getCollection();
        $timesheet_logs = collect($collection)->transformWith(new TimeSheetLogTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));

        return $timesheet_logs;
    }

    public function setTransformersManager($data)
    {
        $collection = $data->getCollection();

        return collect($collection)->transformWith(new TimesheetLogManagerTransform())
            ->paginateWith(new IlluminatePaginatorAdapter($data));
    }

    /**
     * @param $date_time
     * @param $employee_id
     * @param $type_work
     * @return mixed
     */
    private function updateTimeSheet($date_time, $employee_id, $type_work)
    {
        $date = $this->parseDate($date_time);
        $startOrEnd = 'end_time';
        if ($type_work == 'in') {
            $startOrEnd = 'start_time';
        }

        $timesheet = TimeSheet::updateOrCreate(
            ['employee_id' => $employee_id, 'date' => $date],
            [$startOrEnd => $date_time, 'type' => 0]
        );

        $time = TimeSheet::where([
            ['employee_id', $employee_id],
            ['date', $date],
        ])->first(['start_time', 'end_time']);
        if ($time->start_time && $time->end_time) {
            $type = 0;
            $hourRange = (strtotime($time->end_time) - strtotime($time->start_time)) / 3600;

            if ($hourRange >= 8) $type = 1;
            elseif ($hourRange >= 4) $type = 0.5;

            TimeSheet::where([
                ['employee_id', $employee_id],
                ['date', $date],
            ])->update(['type' => $type]);
            $timesheet->type = $type;
        }

        return $timesheet;
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $tsl = $this->query->where('id', $id)->first();
        if (!$tsl) {
            return response()->json([
                'message' => __('message.not_found'),
            ], 404);
        }
        try {
            $data = $request->only($this->model->getFillable());
            $tsl->fill($data);
            $tsl->save();

            $timesheet = $this->updateTimeSheet($tsl->date_time, $tsl->employee_id, $tsl->type);

            return response()->json([
                'message' => __('message.update_success'),
                'data' => [
                    'timesheet_log' => $tsl,
                    'timesheet' => $timesheet,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error($e);

            return $this->errorResponse();
        }
    }

    /**
     * @param $yearMonth
     * @param $perPage
     * @return array
     */
    public function employeesByMonth($yearMonth, $perPage, $employee_name)
    {
        $employees = $this->employee->getEmployeesHasTimesheetLogInMonth($yearMonth, $perPage, $employee_name);

        return $this->setTransformersManager($employees);
    }

    // /**
    //  * @param Request $request
    //  * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
    //  */
    // public function export(Request $request)
    // {
    //     return Excel::download(new BaseExport(new TimeSheetsLog(), ['id', 'name', 'code', 'level', 'parent_id'], $request), 'regions.xlsx');
    // }
}
