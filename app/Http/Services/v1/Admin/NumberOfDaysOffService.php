<?php

namespace App\Http\Services\v1\Admin;

use App\Http\Services\v1\BaseService;
use App\Models\Form\NumberOfDaysOff;
use App\Repositories\Interfaces\NumberOfDaysOffInterface;
use Illuminate\Http\Request;

class NumberOfDaysOffService extends BaseService
{
    /**
     * @var NumberOfDaysOffInterface
     */
    private $numberOfDaysOff;

    public function __construct(NumberOfDaysOffInterface $numberOfDaysOff)
    {
        $this->numberOfDaysOff = $numberOfDaysOff;
    }

    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new NumberOfDaysOff();
    }

    /**
     * @param Request $request
     * @param string $message
     * @return mixed
     */
    public function store(Request $request, $message = '')
    {
        $numberDaysOff = $this->numberOfDaysOff->create([
            'employee_id' => $request->user()->employee_id,
            'date' => $this->parseDate($request->approval_deadline),
            'number_of_minutes' => -$request->number_leave_day,
            'type' => $request->type,
        ]);

        return $numberDaysOff;
    }

    /**
     * @param Request $request
     * @param $leaveForm
     * @return mixed
     */
    public function update(Request $request, $leaveForm)
    {
        $numberDaysOff = $leaveForm->number_of_days_off->update([
            'date' => $this->parseDate($request->approval_deadline),
            'number_of_minutes' => $request->number_leave_day,
        ]);

        return $numberDaysOff;
    }

    public function handleUpdateMonthly($employee, $settingLeaveDayAnnual, $date)
    {
        $minutes = $this->handleConvertDaysToMinutes($settingLeaveDayAnnual->number_of_days);

        return $this->numberOfDaysOff->create([
            'employee_id' => $employee->id,
            'date' => $date,
            'number_of_minutes' => $minutes,
            'type' => NumberOfDaysOff::TYPE['ANNUAL_LEAVE'],
        ]);
    }

    /**
     * @param $days
     * @return float
     */
    public function handleConvertDaysToMinutes($days): float
    {
        $minutes = $days / 12 * 8 * 60;

        return round($minutes, 0);
    }
}
