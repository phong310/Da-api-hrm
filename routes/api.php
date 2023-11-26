<?php

use App\Http\Controllers\Api\v1\User\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\User\CompanyUserController;


// USER ROUTER
Route::namespace('Api\v1\User')->prefix('1.0/user')->group(function () {
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
        // Route::patch('update-password', 'AuthController@updatePassword');

        // Employee
        Route::get('employee/{employee_id}/info', 'EmployeeController@info');
        Route::post('employee/{employee_id}/info', 'EmployeeController@updateInfo');
        Route::get('employee/get-list-by-company', 'EmployeeController@getListByCompany');

        Route::get('account-information/employee/{employee_id}', 'AccountInformationController@byEmployee');
        Route::post('account-information/employee', 'AccountInformationController@createByEmployee');
        Route::patch('account-information/employee/{employee_id}', 'AccountInformationController@updateByEmployee');
    });

    Route::apiResources([
        'role' => 'RoleController',
        'employee' => 'EmployeeController'
    ]);


    Route::apiResources([
        'working-day' => 'WorkingDayUserController',
    ]);
});


// ADMIN ROUTER
Route::namespace('Api\v1\Admin')->prefix('1.0/admin')->group(function () {

    Route::apiResources([
        'settings' => 'SettingController',
        'role' => 'RoleController',
        'employee' => 'EmployeeController'
    ]);
});
