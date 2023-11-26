<?php

namespace App\Http\Services\v1\User;

use App\Exports\EmployeeExport;
use App\Exports\EmployeeExportTemplate;
use App\Imports\EmployeeImport;
use App\Models\Employee;
use App\Models\PersonalInformation;
use App\Transformers\EmployeeTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeService extends UserBaseService
{
    /**
     * @return mixed|void
     */
    protected function setModel()
    {
        $this->model = new Employee();
    }

    public function setTransformersNotPaginate($data)
    {
        $transform = collect($data)->transformWith(new EmployeeTransformer())->toArray();

        return $transform['data'];
    }


    /**
     * @param Request $request
     * @param $employee_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function info(Request $request, $employee_id)
    {
        if ($employee_id == 'me') {
            $employee_id = $request->user()->employee_id;
        }
        $employee = Employee::where('id', $employee_id)
            ->with('personalInformation.country', 'department', 'branch', 'position')
            ->first();

        return response()->json($employee);
    }


    // public function appendFilter()
    // {
    //     $employee_full_name = $this->request->get('full_name');
    //     $employee_email = $this->request->get('email');
    //     $employee_status = $this->request->get('status');
    //     $employee_department = $this->request->get('department');
    //     $employee_branch = $this->request->get('branch');
    //     $employee_position = $this->request->get('position');

    //     if ($employee_full_name) {
    //         $this->query->whereHas('personalInformation', function ($q) use ($employee_full_name) {
    //             $q->whereRaw(
    //                 "TRIM(CONCAT(first_name, ' ', last_name)) like '%{$employee_full_name}%'"
    //             );
    //         });
    //     }
    //     if ($employee_email) {
    //         $this->query->whereHas('personalInformation', function ($q) use ($employee_email) {
    //             $q->whereRaw(
    //                 "TRIM(email) like '%{$employee_email}%'"
    //             );
    //         });
    //     }
    //     if (!is_null($employee_status)) {
    //         $this->query->where('status', $employee_status);
    //     }
    //     if ($employee_department) {
    //         $this->query->whereHas('department', function ($q) use ($employee_department) {
    //             $q->whereRaw(
    //                 "TRIM(id) like '%{$employee_department}%'"
    //             );
    //         });
    //     }
    //     if ($employee_branch) {
    //         $this->query->whereHas('branch', function ($q) use ($employee_branch) {
    //             $q->whereRaw(
    //                 "TRIM(id) like '%{$employee_branch}%'"
    //             );
    //         });
    //     }
    //     if ($employee_position) {
    //         $this->query->whereHas('position', function ($q) use ($employee_position) {
    //             $q->whereRaw(
    //                 "TRIM(id) like '%{$employee_position}%'"
    //             );
    //         });
    //     }


    //     $this->query->with(['information', 'user'])
    //         ->join('personal_information', 'employees.personal_information_id', '=', 'personal_information.id')
    //         ->orderByRaw("TRIM(CONCAT(personal_information.first_name, ' ', personal_information.last_name)) asc");
    // }

    /**
     * @param $data
     * @return mixed
     */
    public function setTransformers($data)
    {
        $collection = $data->getCollection();
        $instances = collect($collection)->transformWith(new EmployeeTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));

        return $instances;
    }

    /**
     * @param Request $request
     * @param $employee_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateInfo(Request $request, $employee_id)
    {
        if ($employee_id == 'me') {
            $employee_id = $request->user()->employee_id;
        }
        $employee = Employee::find($employee_id);
        $personal_information = PersonalInformation::find($employee->personal_information_id);
        $personal_information->first_name = $request->first_name;
        $personal_information->last_name = $request->last_name;
        $personal_information->nickname = $request->nickname;
        $personal_information->email = $request->email;
        $personal_information->phone = $request->phone;
        if ($request->avatar) {
            $personal_information->thumbnail_url = $this->uploadImage($employee_id, $request->avatar);
        }
        $personal_information->save();

        return response()->json([
            'message' => __('message.update_success'),
        ], 200);
    }


    public function uploadImage($employee_id, $avatar)
    {
        $filename = $avatar;
        $folder_name = 'profile/' . $employee_id . '/avatar';
        if (request()->hasFile('avatar')) {
            Storage::disk('public')->delete($filename);
            $avatar = $this->request->file('avatar');
            $filename = Storage::disk('public')->put(
                $folder_name,
                $avatar
            );
        }
        return $filename;
    }

    public function getListByCompany()
    {
        $companyId = $this->getCompanyId();

        $employeeName = $this->request->get('employee_name');
        if ($employeeName) {
            $this->query->whereHas('personalInformation', function ($q) use ($employeeName) {
                $q->whereRaw(
                    "TRIM(CONCAT(first_name, ' ', last_name)) like '%{$employeeName}%'"
                );
            });
        }

        $employees = $this->query->where(['company_id' => $companyId])
            ->join('personal_information', 'employees.personal_information_id', '=', 'personal_information.id')
            ->with(['personalInformation.addresses', 'branch', 'position'])
            ->orderByRaw("TRIM(CONCAT(personal_information.first_name, ' ', personal_information.last_name)) asc")
            ->get();

        return $this->setTransformersNotPaginate($employees);
    }
}
