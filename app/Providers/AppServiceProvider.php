<?php

namespace App\Providers;

use App\Repositories\BranchRepository;
use App\Repositories\DepartmentRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\Interfaces\BranchInterface;
use App\Repositories\Interfaces\DepartmentInterface;
use App\Repositories\Interfaces\EmployeeInterface;
use App\Repositories\Interfaces\PositionInterface;
use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\UserInterface;
use App\Repositories\UserRepository;
use App\Repositories\RoleRepository;
use App\Repositories\Interfaces\RoleInterface;
use App\Repositories\Interfaces\TitleInterface;
use App\Repositories\PositionRepositiory;
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
