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
use App\Repositories\Interfaces\HolidayInterface;
use App\Repositories\Interfaces\NotificationInterface;
use App\Repositories\Interfaces\NumberOfDaysOffInterface;
use App\Repositories\Interfaces\PositionInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\UserInterface;
use App\Repositories\UserRepository;
use App\Repositories\RoleRepository;
use App\Repositories\Interfaces\RoleInterface;
use App\Repositories\Interfaces\TimeSheetInterface;
use App\Repositories\Interfaces\TimeSheetLogInterface;
use App\Repositories\Interfaces\TitleInterface;
use App\Repositories\Interfaces\WorkingDayInterface;
use App\Repositories\LeaveFormRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\NumberOfDaysOffRepository;
use App\Repositories\PositionRepositiory;
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
