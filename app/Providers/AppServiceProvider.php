<?php

namespace App\Providers;

use App\Repositories\BranchRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\Interfaces\BranchInterface;
use App\Repositories\Interfaces\DepartmentInterface;
use App\Repositories\Interfaces\EmployeeInterface;
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
use App\Repositories\NumberOfDaysOffRepository;
use App\Repositories\PositionRepositiory;
use App\Repositories\TimeSheetLogRepository;
use App\Repositories\TimeSheetRepository;
use App\Repositories\TitleRepository;

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
