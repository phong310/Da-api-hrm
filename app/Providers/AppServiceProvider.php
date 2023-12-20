<?php

namespace App\Providers;

use App\Models\Form\CompensatoryLeave;
use App\Repositories\BranchRepository;
use App\Repositories\CompensatoryLeaveRepository;
use App\Repositories\CompensatoryWorkingDayRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\HolidayRepository;
use App\Repositories\Interfaces\BranchInterface;
use App\Repositories\Interfaces\CompensatoryWorkingDayInterface;
use App\Repositories\Interfaces\DepartmentInterface;
use App\Repositories\Interfaces\EmployeeInterface;
use App\Repositories\Interfaces\Forms\CompensatoryLeaveInterface;
use App\Repositories\Interfaces\Forms\LeaveFormInterface;
use App\Repositories\Interfaces\Forms\OvertimeInterface;
use App\Repositories\Interfaces\Forms\RequestChangeTimesheetInterface;
use App\Repositories\Interfaces\HolidayInterface;
use App\Repositories\Interfaces\KindOfLeaveInterface;
use App\Repositories\Interfaces\LaborContract\AllowanceInterface;
use App\Repositories\Interfaces\LaborContract\LaborContractAddressInterface;
use App\Repositories\Interfaces\LaborContract\LaborContractHasAllowanceInterface;
use App\Repositories\Interfaces\LaborContract\LaborContractInterface;
use App\Repositories\Interfaces\LaborContract\LaborContractTypeHasAllowanceInterface;
use App\Repositories\Interfaces\LaborContract\LaborContractTypeInterface;
use App\Repositories\Interfaces\NotificationInterface;
use App\Repositories\Interfaces\NumberOfDaysOffInterface;
use App\Repositories\Interfaces\OtherAllowanceInterface;
use App\Repositories\Interfaces\OvertimeSalaryCoefficientInterface;
use App\Repositories\Interfaces\PositionInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\UserInterface;
use App\Repositories\UserRepository;
use App\Repositories\RoleRepository;
use App\Repositories\Interfaces\RoleInterface;
use App\Repositories\Interfaces\SettingLeaveDayInterface;
use App\Repositories\Interfaces\SettingSalaryTaxCoefficientInterface;
use App\Repositories\Interfaces\SettingTypesOvertimeInterface;
use App\Repositories\Interfaces\TimeSheetInterface;
use App\Repositories\Interfaces\TimeSheetLogInterface;
use App\Repositories\Interfaces\TitleInterface;
use App\Repositories\Interfaces\WorkingDayInterface;
use App\Repositories\KindOfLeaveRepository;
use App\Repositories\LaborContract\AllowanceRepository;
use App\Repositories\LaborContract\LaborContractAddressRepository;
use App\Repositories\LaborContract\LaborContractHasAllowanceRepository;
use App\Repositories\LaborContract\LaborContractRepository;
use App\Repositories\LaborContract\LaborContractTypeHasAllowanceRepository;
use App\Repositories\LaborContract\LaborContractTypeRepository;
use App\Repositories\LeaveFormRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\NumberOfDaysOffRepository;
use App\Repositories\OtherAllowanceRepository;
use App\Repositories\OvertimeRepository;
use App\Repositories\OvertimeSalaryCoefficientRepository;
use App\Repositories\PositionRepositiory;
use App\Repositories\RequestChangeTimesheetRepository;
use App\Repositories\SettingLeaveDayRepository;
use App\Repositories\SettingSalaryTaxCoefficientRepository;
use App\Repositories\SettingTypesOvertimeRepository;
use App\Repositories\TimeSheetLogRepository;
use App\Repositories\TimeSheetRepository;
use App\Repositories\TitleRepository;
use App\Repositories\WorkingDayRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(UserInterface::class, UserRepository::class);
        $this->app->bind(EmployeeInterface::class, EmployeeRepository::class);
        $this->app->bind(RoleInterface::class, RoleRepository::class);
        $this->app->bind(PositionInterface::class, PositionRepositiory::class);
        $this->app->bind(TitleInterface::class, TitleRepository::class);
        $this->app->bind(BranchInterface::class, BranchRepository::class);
        $this->app->bind(DepartmentInterface::class, DepartmentRepository::class);
        $this->app->bind(NumberOfDaysOffInterface::class, NumberOfDaysOffRepository::class);
        $this->app->bind(TimeSheetLogInterface::class, TimeSheetLogRepository::class);
        $this->app->bind(TimeSheetInterface::class, TimeSheetRepository::class);
        $this->app->bind(LeaveFormInterface::class, LeaveFormRepository::class);
        $this->app->bind(WorkingDayInterface::class, WorkingDayRepository::class);
        $this->app->bind(CompensatoryWorkingDayInterface::class, CompensatoryWorkingDayRepository::class);
        $this->app->bind(HolidayInterface::class, HolidayRepository::class);
        $this->app->bind(CompensatoryLeaveInterface::class, CompensatoryLeaveRepository::class);
        $this->app->bind(NotificationInterface::class, NotificationRepository::class);
        $this->app->bind(RequestChangeTimesheetInterface::class, RequestChangeTimesheetRepository::class);
        $this->app->bind(KindOfLeaveInterface::class, KindOfLeaveRepository::class);
        $this->app->bind(OvertimeInterface::class, OvertimeRepository::class);
        $this->app->bind(SettingTypesOvertimeInterface::class, SettingTypesOvertimeRepository::class);
        $this->app->bind(SettingLeaveDayInterface::class, SettingLeaveDayRepository::class);
        $this->app->bind(OvertimeSalaryCoefficientInterface::class, OvertimeSalaryCoefficientRepository::class);
        $this->app->bind(LaborContractInterface::class, LaborContractRepository::class);
        $this->app->bind(LaborContractAddressInterface::class, LaborContractAddressRepository::class);
        $this->app->bind(LaborContractHasAllowanceInterface::class, LaborContractHasAllowanceRepository::class);
        $this->app->bind(LaborContractTypeInterface::class, LaborContractTypeRepository::class);
        $this->app->bind(SettingSalaryTaxCoefficientInterface::class,SettingSalaryTaxCoefficientRepository::class);
        $this->app->bind(AllowanceInterface::class, AllowanceRepository::class);
        $this->app->bind(LaborContractTypeHasAllowanceInterface::class, LaborContractTypeHasAllowanceRepository::class);
        $this->app->bind(OtherAllowanceInterface::class, OtherAllowanceRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
