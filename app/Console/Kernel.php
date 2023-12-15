<?php

namespace App\Console;

use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        Company::query()->orderBy('id')
            ->with(['setting'])
            ->chunk(100, function ($companies) use ($schedule) {
                foreach ($companies as $company) {
                    $time_zone = 'UTC';
                    $yesterday = Carbon::yesterday()->toDateString();
                    if ($company->setting) {
                        $time_zone = $company->setting->time_zone;
                        $dateNow = Carbon::now()->timezone($time_zone);
                        $yesterday = $dateNow->subDay()->toDateString();
                        $year = $dateNow->year;
                        $month = $dateNow->month;
                    }
                    $schedule->command('command:update-timesheets ' . $company->id . ' ' . $yesterday)
                        ->dailyAt('03:00')
                        //                        ->everyMinute();
                        ->timezone($time_zone);
                    //                    $schedule->command('command:monthly-update-numberOfDaysOff '.$company->id.' '.$month.' '.$year)
                    //                        ->monthlyOn(1, '03:00')
                    //                        ->timezone($time_zone);
                    $schedule->command('command:update-LaborContract')->dailyAt('01:00');
                }
            });
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
