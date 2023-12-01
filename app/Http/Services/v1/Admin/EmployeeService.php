<?php

namespace App\Http\Services\v1\Admin;

use App\Exports\EmployeeExport;
use App\Exports\EmployeeExportTemplate;
use App\Http\Services\v1\BaseService;
use App\Imports\EmployeeImport;
use App\Models\Address;
use App\Models\BankAccount;
use App\Models\Education;
use App\Models\Employee;
use App\Models\IdentificationCard;
use App\Models\PersonalInformation;
use App\Models\User;
use App\Repositories\Interfaces\BranchInterface;
use App\Repositories\Interfaces\DepartmentInterface;
use App\Repositories\Interfaces\PositionInterface;
use App\Repositories\Interfaces\RoleInterface;
use App\Repositories\Interfaces\TitleInterface;
use App\Repositories\Interfaces\UserInterface;
use App\Transformers\EmployeeTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeService extends BaseService
{
    protected $user;

    protected $model;

    protected $query;

    protected $request;

    protected $title;

    protected $positon;

    protected $branch;

    protected $department;

    protected $role;

    /**
     * @return mixed|void
     */

    public function setModel()
    {
        $this->model = new Employee();
    }

    protected function setQuery()
    {
        $this->query = $this->model->query();
    }

    public function __construct(
        UserInterface $user,
        TitleInterface $title,
        PositionInterface $positon,
        BranchInterface $branch,
        DepartmentInterface $department,
        RoleInterface $role
    ) {
        $this->user = $user;
        $this->request = request();
        $this->setModel();
        $this->setQuery();
        $this->title = $title;
        $this->positon = $positon;
        $this->branch = $branch;
        $this->department = $department;
        $this->role = $role;
    }

    public function appendFilter()
    {
        $this->query->with(['information', 'user']);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Http\JsonResponse|mixed|object|null
     */
    public function show(Request $request, $id)
    {
        if ($id == 'me') {
            $id = $request->user()->employee_id;

            return $this->model
                ->with([
                    'information', 'information.addresses', 'information.educations',
                    'information.identificationCards',
                    'information.job', 'information.country', 'information.educationLevel', 'information.title',
                    'bankAccount', 'position', 'department', 'branch',
                ])
                ->where('id', $id)
                ->first();
        }

        $instance = $this->query->find($id);

        if (!$instance) {
            return response()->json([
                'message' => __('message.not_found'),
            ], 404);
        }

        return $this->model
            ->with([
                'information', 'information.addresses', 'information.educations', 'information.identificationCards',
                'information.job', 'information.country', 'information.educationLevel', 'information.title',
                'bankAccount', 'position', 'department', 'branch', 'user',
            ])
            ->where('id', $id)
            ->first();
    }

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
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $employeeData = $request->only($this->model->getFillable());

        $instance = $this->query->find($id);
        $personal_information_id = $instance['personal_information_id'];
        try {
            DB::beginTransaction();
            $instance->fill($employeeData);
            if (array_key_exists('information', $data)) {
                $information = $data['information'];
                $instance->information()
                    ->update(collect($information)
                        ->only((new PersonalInformation())->getFillable())->all());
                if (array_key_exists('addresses', $information)) {
                    $addresses = $information['addresses'];
                    foreach ($addresses as $address) {
                        if (array_key_exists('id', $address)) {
                            Address::find($address['id'])
                                ->update(collect($address)
                                    ->only((new Address())->getFillable())->all());
                        } else {
                            Address::create([
                                'province_id' => $address['province_id'],
                                'district_id' => $address['district_id'],
                                'ward_id' => $address['ward_id'],
                                'address' => $address['address'],
                                'personal_information_id' => $personal_information_id,
                            ]);
                        }
                    }
                }

                if (array_key_exists('educations', $information)) {
                    $educations = $information['educations'];
                    foreach ($educations as $education) {
                        if (array_key_exists('id', $education)) {
                            Education::find($education['id'])
                                ->update(collect($education)
                                    ->only((new Education())->getFillable())->all());
                        } else {
                            Education::create([
                                'school_name' => $education['school_name'],
                                'from_date' => $education['from_date'],
                                'to_date' => $education['to_date'],
                                'description' => $education['description'],
                                'personal_information_id' => $personal_information_id,
                            ]);
                        }
                    }
                }

                if (array_key_exists('identification_cards', $information)) {
                    $identificationCards = $information['identification_cards'];
                    foreach ($identificationCards as $idenCard) {
                        if (array_key_exists('id', $idenCard)) {
                            IdentificationCard::find($idenCard['id'])
                                ->update(collect($idenCard)
                                    ->only((new IdentificationCard())->getFillable())->all());
                        } else {
                            IdentificationCard::create([
                                'ID_no' => $idenCard['ID_no'],
                                'issued_date' => $idenCard['issued_date'],
                                'issued_by' => $idenCard['issued_by'],
                                'ID_expire' => $idenCard['ID_expire'],
                                'type' => $idenCard['type'],
                                'personal_information_id' => $idenCard['personal_information_id'],
                            ]);
                        }
                    }
                }
            }
            if (array_key_exists('bank_account', $data)) {
                $instance->bankAccount()
                    ->update(collect($data['bank_account'])
                        ->only((new BankAccount())->getFillable())->all());
            }

            if (array_key_exists('user', $data)) {
                $instance->user()
                    ->update(collect($data['user'])
                        ->only((new User())->getFillable())->all());
            }

            $instance->save();
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            return $this->errorResponse();
        }

        return response()->json([
            'message' => 'Update success',
        ], 200);
    }

    /**
     * @param Request $request
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $message = '')
    {
        $company_id = $this->getCompanyId();
        try {
            DB::beginTransaction();
            $data = $request->all();
            $personalInfoData = collect($data)->only((new PersonalInformation())->getFillable())->all();
            $personalInfo = PersonalInformation::create($personalInfoData);
            $data['personal_information_id'] = $personalInfo->id;
            $data['type'] = $data['iDenType'];

            //IdentificationCard
            $identificationCardData = collect($data)->only((new IdentificationCard())->getFillable())->all();
            if ($identificationCardData['ID_expire']) {
                IdentificationCard::create($identificationCardData);
            }

            //Address
            $data['type'] = $data['addressType'];
            $addressData = collect($data)->only((new Address())->getFillable())->all();
            if ($addressData['type']) {
                Address::create($addressData);
            }

            //Education
            //            $educationData = collect($data)->only((new Education())->getFillable())->all();
            //            if ($educationData['school_name']) {
            //                Education::create($educationData);
            //            }

            //Employee
            $employeeData = collect($data)->only((new Employee())->getFillable())->all();
            $employeeData['company_id'] = $company_id;
            $employee = Employee::create($employeeData);
            $data['employee_id'] = $employee->id;

            //BankAccount
            //            $bankAccountData = collect($data)->only((new BankAccount())->getFillable())->all();
            //            if ($bankAccountData['account_number']) {
            //                BankAccount::create($bankAccountData);
            //            }

            //User
            $data['email'] = $data['user_email'];
            $data['password'] = bcrypt($data['password']);
            $userData = collect($data)->only((new User())->getFillable())->all();
            $userData['company_id'] = $company_id;
            $user = User::create($userData);
            $role_id = $request->role_id;
            $user->assignRole($role_id);

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());

            return response()->json(['error' =>  __('message.server_error')], 500);
        }

        return response()->json([
            'message' => __('message.created_success'),
        ], 200);
    }

    // /**
    //  * @param Request $request
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function import(Request $request)
    // {
    //     $import = new EmployeeImport;
    //     Excel::import($import, request()->file('file'));
    //     // Log::error($import->errors());
    //     return response()->json([
    //         'message' => 'Import success',
    //     ], 200);
    // }

    // /**
    //  * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
    //  */
    // public function export()
    // {
    //     return Excel::download(new EmployeeExport(), 'employees.xlsx');
    // }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    // public function exportTemplate(Request $request)
    // {
    //     return Excel::download(new EmployeeExportTemplate($this->title, $this->positon, $this->branch, $this->department, $this->role), 'employees.xlsx');
    // }

    public function superCreateEmployee(Request $request)
    {

        DB::beginTransaction();
        try {
            $data = $request->all();
            $personalInfoData = collect($data)->only((new PersonalInformation())->getFillable())->all();
            $personalInfo = PersonalInformation::create($personalInfoData);
            $data['personal_information_id'] = $personalInfo->id;
            $data['type'] = $data['iDenType'];

            //IdentificationCard
            $identificationCardData = collect($data)->only((new IdentificationCard())->getFillable())->all();
            if ($identificationCardData['ID_expire']) {
                IdentificationCard::create($identificationCardData);
            }

            //Address
            $data['type'] = $data['addressType'];
            $addressData = collect($data)->only((new Address())->getFillable())->all();
            if ($addressData['type']) {
                Address::create($addressData);
            }

            // Employee
            $employeeData = collect($data)->only((new Employee())->getFillable())->all();
            $employeeData['company_id'] = $request['company_id'];
            $employee = Employee::create($employeeData);

            // User
            $data['employee_id'] = $employee->id;
            $data['email'] = $data['user_email'];
            $data['password'] = bcrypt($data['password']);
            $userData = collect($data)->only((new User())->getFillable())->all();
            $userData['company_id'] = $request['company_id'];
            $user = $this->user->showUserNewCreateByCompanyId($request['company_id']);

            if ($user) {
                $user->fill($userData);
                $user->save();
            } else {
                $user = User::create($userData);
                $role_id = $request->role_id;
                $user->assignRole($role_id);
            }

            DB::commit();
        } catch (\Exception $th) {
            DB::rollBack();
            Log::error($th->getMessage());

            return response()->json(['error' =>  __('message.server_error')], 500);
        }
        return response()->json(
            [
                'message' => __('message.created_success'),
            ],
            200
        );
    }
}
