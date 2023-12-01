<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Repositories\Interfaces\EmployeeInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EmployeeRepository implements EmployeeInterface
{
    /**
     * @var Employee
     */
    protected $employee;

    /**
     * @param Employee $employee
     */
    public function __construct(Employee $employee)
    {
        $this->employee = $employee;
    }

    public function store($data)
    {
        return $this->employee::query()->create($data);
    }

    public function show($id)
    {
        return $this->employee::query()->where(['id' => $id])->first();
    }

    public function getEmployeesHasPermission($permission)
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        $employees = Employee::query()
            ->where(['company_id' => $companyId])
            ->whereHas('user', function ($q) use ($permission) {
                $q->permission($permission);
            })
            ->with(['personalInformation'])
            ->get();

        $dataEmployees = [];

        foreach ($employees as $key => $employee) {
            $dataEmployees[$key]['id'] = $employee->id;
            $dataEmployees[$key]['full_name'] = $employee->personalInformation->full_name;
        }

        return $dataEmployees;
    }

    public function getEmployeesHasTimesheetInMonth($perPage, $employee_name)
    {
        $companyId = Auth::user()->company_id;

        $query = Employee::query();

        if (!is_null($employee_name)) {
            $query->whereHas('personalInformation', function ($q) use ($employee_name) {
                $q->whereRaw(
                    "TRIM(CONCAT(first_name, ' ', last_name)) like '%{$employee_name}%'"
                );
            });
        }

        return $query->where(['company_id' => $companyId])
            ->join('personal_information', 'personal_information.id', '=', 'employees.personal_information_id')
            ->orderByRaw("CONCAT(first_name, ' ', last_name)")
            ->with(['position', 'department', 'personalInformation'])
            ->paginate($perPage ?? 10);
    }

    public function getEmployeesHasTimesheetLogInMonth($date, $perPage, $employee_name)
    {
        $today = Carbon::now();
        $companyId = Auth::user()->company_id;
        $month = $today->month;
        $year = $today->year;
        $query = Employee::query();

        if ($date) {
            $dataArray = explode('-', $date);
            $month = $dataArray[1];
            $year = $dataArray[0];
        }

        if (!is_null($employee_name)) {
            $query->whereHas('personalInformation', function ($q) use ($employee_name) {
                $q->whereRaw(
                    "TRIM(CONCAT(first_name, ' ', last_name)) like '%{$employee_name}%'"
                );
            });
        }

        return $query->where(['company_id' => $companyId])
            ->join('personal_information', 'personal_information.id', '=', 'employees.personal_information_id')
            ->orderByRaw("CONCAT(first_name, ' ', last_name)")
            ->with('timesheetsLogs', function ($q) use ($month, $year) {
                $q->whereYear('date_time', '=', $year)
                    ->whereMonth('date_time', '=', $month);
            })->paginate($perPage ?? 10);
    }
}
