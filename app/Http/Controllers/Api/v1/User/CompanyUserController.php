<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Api\v1\Admin\BaseMasterController;
use App\Http\Requests\Admin\CompanyNewRequest;
use App\Http\Requests\Admin\CompanyRequest;
use App\Http\Requests\Admin\CreateAccountRequest;
use App\Http\Requests\Admin\DepartmentBranchRequest;
use App\Http\Requests\User\StoreWorkingDayUserRequest;
use App\Http\Services\v1\Admin\CompanyService;
use App\Http\Services\v1\Admin\BranchService;
use App\Http\Services\v1\Admin\DepartmentService;
use App\Http\Services\v1\Admin\PositionService;
use App\Http\Services\v1\Admin\SettingService;
use App\Http\Services\v1\Admin\RoleService;
use App\Http\Services\v1\Admin\TitleService;
use App\Http\Services\v1\Admin\UserService;
use App\Http\Services\v1\User\WorkingDayUserService;
use Illuminate\Support\Arr;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompanyUserController extends BaseMasterController
{
    /**
     * @param CompanyService $companyService
     */

    /**
     * @var BranchService
     */
    protected $branchService;
    /**
     * @var DepartmentService
     */
    protected $departmentService;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var RoleService
     */
    protected $roleService;

    /**
     * @var SettingService
     */
    protected $settingService;

    /**
     * @var WorkingDayUserService
     */
    protected $workingDayUserService;
    
    protected $titleService;

    protected $positionService;

    /**
     * @param CompanyService $companyService
     * @param BranchService $branchService
     * @param DepartmentService $departmentService
     * @param UserService $userService
     * @param SettingService $settingService
     */
    public function __construct(
        CompanyService        $companyService,
        BranchService         $branchService,
        DepartmentService      $departmentService,
        SettingService         $settingService,
        WorkingDayUserService  $workingDayUserService,
        UserService           $userService,
        RoleService           $roleService,
        TitleService          $titleService,
        PositionService       $positionService

    ) {
        $this->service = $companyService;
        $this->branchService = $branchService;
        $this->departmentService = $departmentService;
        $this->settingService = $settingService;
        $this->workingDayUserService = $workingDayUserService;
        $this->userService = $userService;
        $this->roleService = $roleService;
        $this->titleService = $titleService;
        $this ->positionService = $positionService;
    }
    /**
     * @param CompanyRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CompanyRequest $request)
    {
        return $this->service->store($request);
    }


    /**
     * @param CompanyRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CompanyRequest $request, $id)
    {
        return $this->service->update($request, $id);
    }

    public function getAccountsByCompanyId($companyId)
    {
        $users = $this->userService->getByCompanyId($companyId);

        return response()->json([
            'data' => $users,
        ], 200);
    }

    public function getDepartmentBranchByCompanyId($companyId)
    {
        if (!$companyId) {
            return response()->json([
                'message' => ("Company not found")
            ], 200);
        }
        $branchs = $this->branchService->getByCompanyId($companyId);
        $departments = $this->departmentService->getByCompanyId($companyId);

        return response()->json([
            'branchs' => $branchs,
            'department' => $departments
        ], 200);
    }

    public function createNewCompany(CompanyNewRequest $request)
    {
        DB::beginTransaction();
        try {
            $account = $request->all();
            $company = $this->service->storeNew($request);
            Log::info(
                $company->id
            );
            $branchs = $request->get('branchs');
            $departments = $request->get('departments');
            $positions = $request->get('positions');
            $titles = $request->get('titles');
            $system_setting = $request->get('system_setting');
            $system_setting = json_decode($system_setting);
            $dataSetting = [
                'company_id' => $company->id,
                'time_zone' => $system_setting->time_zone,
                'format_date' => $system_setting->format_date,
                'locale' =>  $system_setting->locale
            ];

            $dataWorkingDay = [
                'company_id' => $company->id,
                'day_in_week_id' => $system_setting->day_in_week_id,
                'name' => $system_setting->name,
                'start_time' => $system_setting->start_time,
                'end_time' => $system_setting->end_time,
                'start_lunch_break' => $system_setting->start_lunch_break,
                'end_lunch_break' => $system_setting->end_lunch_break,
                'type' => $system_setting->type,
            ];
            $this->titleService->updateMulti($titles, $company->id);
            $this->positionService->updateMulti($positions, $company->id);
            $this->branchService->updateMulti($branchs, $company->id);
            $this->departmentService->updateMulti($departments, $company->id);
            $this->service->createSettingByCompany($dataSetting);
            $this->service->createWorkingDayByCompany($dataWorkingDay);
            $this->roleService->storeRoleDefault($company->id);
            $this->userService->store($account, $company->id);

            DB::commit();
            return response()->json([
                'message' => __('message.created_success'),
                'status' => Response::HTTP_OK,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json([
                'message' => __('message.server_error'),
            ], 403);
        }
    }

    public function checkCompany(CompanyRequest $request)
    {
        return response()->json([
            'status' => Response::HTTP_OK,
        ]);
    }

    public function checkDepartmentBranch(DepartmentBranchRequest $request)
    {
        return response()->json([
            'status' => Response::HTTP_OK,
        ]);
    }

    public function checkPositionTitles()
    {
        return response()->json([
            'status' => Response::HTTP_OK,
        ]);
    }

    /**
     * @param DepartmentBranchRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function departmentBranch(DepartmentBranchRequest $request, $id)
    {

        $branchs = $request->get('branchs');
        $departments = $request->get('departments');

        $this->branchService->storeMulti($branchs, $id);
        $this->departmentService->storeMulti($departments, $id);

        return response()->json([
            'message' => __('message.created_success'),
            'status' => Response::HTTP_OK,
        ]);
    }


    public function updateDepartmentBranch(DepartmentBranchRequest $request, $id)
    {
        $branchs = $request->get('branchs');
        $departments = $request->get('departments');


        $this->branchService->updateMulti($branchs, $id);
        $this->departmentService->updateMulti($departments, $id);

        return response()->json([
            'message' => __('message.update_success'),
            'status' => Response::HTTP_OK,
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function companySetting(StoreWorkingDayUserRequest $request)
    {
        $this->settingService->store($request);
        $this->workingDayUserService->_store($request);

        return response()->json([
            'message' => __('message.created_success'),
            'status' => Response::HTTP_OK,
        ]);
    }

    public function getCompanySettingWorkingDay($companyId)
    {
        $settings = $this->settingService->getByCompanyId($companyId);
        $workingDays = $this->workingDayUserService->getByCompanyId($companyId);
        $workingDaysCustom = count($workingDays) ? [
            'name' => Arr::pluck($workingDays, 'name'),
            'day_in_week_id' => Arr::pluck($workingDays, 'day_in_week_id'),
            'type' => $workingDays[0]['type'],
            'start_time' => $workingDays[0]['start_time'],
            'end_time' => $workingDays[0]['end_time'],
            'start_lunch_break' => $workingDays[0]['start_lunch_break'],
            'end_lunch_break' => $workingDays[0]['end_lunch_break'],
        ] : null;

        return response()->json([
            'system_setting' => $settings,
            'working_day' => $workingDaysCustom
        ]);
    }

    public function updateCompanySettingWorkingDay(StoreWorkingDayUserRequest $request, $companyId)
    {
        $data = $request->all();
        $dataSetting = [
            'time_zone' => $data['time_zone'],
            'format_date' => $data['format_date'],
            'locale' => $data['locale']
        ];

        $dataWorkingDay = [
            'day_in_week_id' => $data['day_in_week_id'],
            'name' => $data['name'],
            'start_time' => $data['start_time'],
            'end_time' => $data['end_time'],
            'start_lunch_break' => $data['start_lunch_break'],
            'end_lunch_break' => $data['end_lunch_break'],
            'type' => $data['type']
        ];


        $this->settingService->updateByCompanyId($dataSetting, $companyId);
        $this->workingDayUserService->updateByCompanyId($dataWorkingDay, $companyId);

        return response()->json([
            'message' => __('message.update_success'),
            'status' => Response::HTTP_OK,
        ]);
    }

    /**
     * @param CreateAccountRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function createAccount(CreateAccountRequest $request, $id)
    {
        try {
            $account = $request->all();
            $this->roleService->storeRoleDefault($id);
            $this->userService->store($account, $id);

            return response()->json([
                'message' => __('message.created_success'),
                'status' => Response::HTTP_OK,
            ]);
        } catch (\Exception $exception) {
            Log::info($exception->getMessage());
        }
    }

    public function updateAccountByCompanyId(CreateAccountRequest $request, $companyId, $userId)
    {
        try {
            $account = $request->all();
            $this->userService->update($account, $companyId, $userId);

            return response()->json([
                'message' => __('message.update_success'),
                'status' => Response::HTTP_OK,
            ]);
        } catch (\Exception $exception) {
            return ($exception->getMessage());
        }
    }
}
