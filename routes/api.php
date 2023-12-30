<?php

use App\Http\Controllers\Api\v1\Admin\UserController;
use App\Http\Controllers\Api\v1\User\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\User\CompanyUserController;
use App\Http\Controllers\Api\v1\User\LeaveFormUserController;
use App\Http\Controllers\Api\v1\User\NotificationController as UserNotificationController;


// USER ROUTER
Route::namespace('Api\v1\User')->middleware(['language'])->prefix('1.0/user')->group(function () {
    Route::post('login', 'AuthController@login');
    Route::post('refresh-token', 'AuthController@refreshToken');

    // Company
    Route::post('companies/create', [CompanyUserController::class, 'store']);
    Route::post('companies/check_company', [CompanyUserController::class, 'checkCompany']);
    Route::patch('companies/create/{id}', [CompanyUserController::class, 'update']);
    Route::get('companies/{id}', 'CompanyUserController@show');

    Route::post('companies/create-new-company', [CompanyUserController::class, 'createNewCompany']);
    Route::post('companies/{id}/department-branch', [CompanyUserController::class, 'departmentBranch']);
    Route::post('companies/department-branch', [CompanyUserController::class, 'checkDepartmentBranch']);
    Route::patch('companies/{id}/department-branch', [CompanyUserController::class, 'updateDepartmentBranch']);
    Route::get('companies/{id}/department-branch', [CompanyUserController::class, 'getDepartmentBranchByCompanyId']);
    Route::post('companies/check-position-titles', [CompanyUserController::class, 'checkPositionTitles']);

    Route::get('companies/setting-workingDay/{companyId}', [CompanyUserController::class, 'getCompanySettingWorkingDay']);
    Route::patch('companies/setting-workingDay/{companyId}', [CompanyUserController::class, 'updateCompanySettingWorkingDay']);
    Route::post('companies/setting-workingDay', [CompanyUserController::class, 'companySetting']);

    Route::post('companies/{id}/create-account', [CompanyUserController::class, 'createAccount']);
    Route::get('companies/{companyId}/get-accounts', [CompanyUserController::class, 'getAccountsByCompanyId']);
    Route::patch('companies/{companyId}/create-account/{user_id}', [CompanyUserController::class, 'updateAccountByCompanyId']);

    Route::get('days-in-week', 'DaysInWeekUserController@index');

    Route::middleware(['auth:api'])->group(function () {
        Route::post('logout', 'AuthController@logout');

        // Profile personal
        Route::get('me', 'AuthController@me');
        Route::get('me/info', 'AuthController@info');
        Route::get('me/banking', 'BankAccountController@banking');
        Route::post('me/update-banking', 'BankAccountController@updateBanking');
        Route::get('me/identification', 'IdentificationController@identificationCard');
        Route::post('me/update-identification', 'IdentificationController@updateIdentification');
        Route::get('me/address', 'AddressController@address');
        Route::post('me/update-address/{employee_id}', 'AddressController@updateAddress');
        Route::patch('update-password', 'AuthController@updatePassword');

        // Employee
        Route::get('employee/{employee_id}/info', 'EmployeeController@info');
        Route::post('employee/{employee_id}/info', 'EmployeeController@updateInfo');
        Route::get('employee/get-list-by-company', 'EmployeeController@getListByCompany');

        Route::get('account-information/employee/{employee_id}', 'AccountInformationController@byEmployee');
        Route::post('account-information/employee', 'AccountInformationController@createByEmployee');
        Route::patch('account-information/employee/{employee_id}', 'AccountInformationController@updateByEmployee');

        // TimeKeeping
        Route::get('timekeeping/today', 'TimeKeepingController@todayTimeSheetLog');
        Route::get('timekeeping/check-has-timekeeping', 'TimeKeepingController@checkHasTimekeeping');
        Route::get('dashboard/calculate', 'DashboardController@index');
        Route::get('timekeeping/total-time-in-month', 'TimeKeepingController@totalTimeInMonth');

        Route::get('timesheets/{month}', 'TimeSheetController@employeesByMonth');
        Route::get('timesheet/check-has-form-by-date/{date}', 'TimeSheetController@checkHasFormByDate');
        Route::get('timesheet-log/{month}', 'TimeSheetLogController@employeesByMonth');

        Route::get('day-off/remaining-days-off', 'NumberOfDaysOffUserController@remainingDaysOff');

        Route::group(['middleware' => ['permission:employees.manage']], function () {
            Route::group(['middleware' => ['permission:employees.list']], function () {
                Route::get('bank-account/employee/{employee_id}', 'BankAccountController@byEmployee');
                Route::get('identification-card/employee/{employee_id}', 'IdentificationController@byEmployee');
                Route::get('address/employee/{employee_id}', 'AddressController@byEmployee');
                Route::get('education/employee/{employee_id}', 'EducationController@byEmployee');
                Route::get('number-of-days-off/remaining-days-off', 'NumberOfDaysOffUserController@remainingDaysOff');
                Route::get('number-of-days-off/employee/{employee_id}', 'NumberOfDaysOffUserController@byEmployee');
                Route::get('relatives/employee/{employee_id}', 'RelativeController@byEmployee');
            });
            Route::patch('bank-account/employee/{employee_id}', 'BankAccountController@updateByEmployee');
            Route::post('bank-account/employee', 'BankAccountController@storeByEmployee');

            Route::patch('identification-card/employee/{employee_id}', 'IdentificationController@updateByEmployee');
            Route::patch('address/employee/{employee_id}', 'AddressController@updateByEmployee');

            Route::post('education/employee', 'EducationController@createByEmployee');
            Route::patch('education/employee/{employee_id}', 'EducationController@updateByEmployee');
            Route::delete('education/employee/{employee_id}/{education_id}', 'EducationController@deleteByEmployee');

            Route::post('relatives/employee', 'RelativeController@createByEmployee');
            Route::post('relatives/employee/{relative_id}', 'RelativeController@updateByEmployee');
            Route::delete('relatives/employee/{employee_id}/{relative_id}', 'RelativeController@deleteByEmployee');

            Route::apiResources([
                'role' => 'RoleController',
                'employee' => 'EmployeeController',
                'position' => 'PositionController',
                'department' => 'DepartmentController',
                'branch' => 'BranchController',
                'country' => 'CountryController',
                'education-level' => 'EducationLevelController',
                'number-of-days-off' => 'NumberOfDaysOffUserController',
                'relatives' => 'RelativeController',
                
            ]);
        });
        Route::get('labor-contract/user', 'LaborContractController@getEmployeeLabor');
        Route::get('labor-contract/user/{id}', 'LaborContractController@showMySelf');
        Route::get('labor-contract/count-by-employee/{employeeId}', 'LaborContractController@countByEmployee');
        Route::get('labor-contract/has-labor-contract-active/{employeeId}', 'LaborContractController@hasLaborContractActive');
        Route::get('holiday', 'HolidayController@index');
        Route::get('/leave-form/information', [LeaveFormUserController::class, 'getListLeaveAppInformation']);
        Route::get('setting-types-overtime', 'SettingTypesOvertimeController@index');

        Route::apiResources([
            'request-change-timesheet' => 'RequestChangeTimeSheetController', 
            'working-day' => 'WorkingDayUserController',
            'timekeeping' => 'TimeKeepingController',
            'compensatory-working-day' => 'CompensatoryWorkingDayController',
            'manager/form' => 'ManagerController',
            'leave-form' => 'LeaveFormUserController',
            'overtime-form' => 'OverTimeController',
            'compensatory-leave' => 'CompensatoryLeaveController',
            'labor-contract' => 'LaborContractController',
            'labor-contract-type' => 'LaborContractTypeController',
            'allowance' => 'AllowanceController',
        ]);
        // Loại nghỉ phép
        Route::get('kind-of-leave', 'KindOfLeaveController@index');
        Route::get('manager/approvers', 'ManagerController@getApprovers');
        Route::patch('manager/form/action/{id}', 'ManagerController@handleForm');



        
        Route::get('notifications', [UserNotificationController::class, 'index']);
        Route::post('notifications', [UserNotificationController::class, 'store']);
        Route::get('notifications/new-count', [UserNotificationController::class, 'newCount']);
        Route::patch('notifications/mark-as-read/{id}', [UserNotificationController::class, 'markAsRead']);
        Route::patch('notifications/mark-all-as-read', [UserNotificationController::class, 'markAllAsRead']);
        Route::patch('notifications/mark-as-seen', [UserNotificationController::class, 'markAsSeen']);

        Route::patch('overtime-form/cancel/{id}', [\App\Http\Controllers\Api\v1\User\OverTimeController::class, 'cancel']);
        Route::patch('leave-form/cancel/{id}', [\App\Http\Controllers\Api\v1\User\LeaveFormUserController::class, 'cancel']);
        Route::patch('request-change-timesheet/cancel/{id}', [\App\Http\Controllers\Api\v1\User\RequestChangeTimeSheetController::class, 'cancel']);
        Route::patch('compensatory-form/cancel/{id}', [\App\Http\Controllers\Api\v1\User\CompensatoryLeaveController::class, 'cancel']);

        Route::patch('reset-password', [UserController::class, 'resetEmployeePassword']);
    });
});


// ADMIN ROUTER
Route::namespace('Api\v1\Admin')->middleware(['language'])->prefix('1.0/admin')->group(function () {
    Route::post('login', 'AuthController@login');
    Route::post('refresh-token', 'AuthController@refreshToken');


    Route::middleware(['auth:api', 'role:super_admin|admin'])->group(function () {

        Route::get('settings/get-by-company/{companyId}', 'SettingController@getByCompanyId');
        Route::get('setting-types-overtime/show-by-type/{type}', 'SettingTypesOvertimeController@showByType');
        Route::get('salary-tax-coefficient-settings/get-by-company', 'SettingSalaryTaxCoefficientController@showSettingCoefficientByCompany');

        Route::post('employee/import', 'EmployeeController@import');
        Route::get('employee/export', 'EmployeeController@export');
        Route::get('employee/template', 'EmployeeController@exportTemplate');

        Route::apiResources([
            'settings' => 'SettingController',
            'role' => 'RoleController',
            'employee' => 'EmployeeController',
            'position' => 'PositionController',
            'department' => 'DepartmentController',
            'branch' => 'BranchController',
            'title' => 'TitleController',
            'country' => 'CountryController',
            'working-day' => 'WorkingDayController',
            'timesheet' => 'TimeSheetController',
            'kind-of-leave' => 'KindOfLeaveController',
            'companies' => 'CompanyController',
            'holiday' => 'HolidayController',
            'days-in-week' => 'DaysInWeekController',
            'compensatory-working-day' => 'CompensatoryWorkingDayController',
            'setting-types-overtime' => 'SettingTypesOvertimeController',
            'labor-contract-type' => 'LaborContractTypeController',
            'allowance' => 'AllowanceController',
            'other-allowance' => 'OtherAllowanceController',
        ]);

        Route::post('companies/{company_id}/info', 'CompanyController@updateInfo');
    });
});
