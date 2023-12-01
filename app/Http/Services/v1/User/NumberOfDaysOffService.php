<?php

namespace App\Http\Services\v1\User;

use App\Models\Form\NumberOfDaysOff;
use App\Repositories\Interfaces\NumberOfDaysOffInterface;
use Illuminate\Http\Request;

class NumberOfDaysOffService extends UserBaseService
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
    public function appendFilter()
    {
        $this->query->where(['employee_id' => $this->request->employee_id]);
    }
    /**
     * @param $employee_id
     * @param $date
     * @param $number_of_minutes
     * @param $type
     * @return mixed
     */
    public function store($employee_id, $date, $number_of_minutes, $type)
    {
        return $this->numberOfDaysOff->create([
            'employee_id' => $employee_id,
            'date' => $date,
            'number_of_minutes' => $number_of_minutes,
            'type' => $type,
        ]);
    }

    /**
     * @param $employee_id
     * @return array
     */
    public function getNumberOfDaysOffOfEmployee($employee_id)
    {
        $type_annual_leave = NumberOfDaysOff::query()
            ->where(['employee_id' => $employee_id])
            ->where(['type' => NumberOfDaysOff::TYPE['ANNUAL_LEAVE']])
            ->sum('number_of_minutes');

        $type_leave_form = NumberOfDaysOff::query()
            ->where(['employee_id' => $employee_id])
            ->where(['type' => NumberOfDaysOff::TYPE['LEAVE_FROM']])
            ->sum('number_of_minutes');

        return [
            'annual_leave'  => $type_annual_leave,
            'leave_form'  => $type_leave_form,
        ];
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
}
