<?php

namespace App\Console\Commands;

use App\Http\Services\v1\Admin\CompanyService;
use App\Http\Services\v1\Admin\HolidayService;
use App\Http\Services\v1\Admin\TimeSheetService;
use App\Http\Services\v1\Admin\WorkingDayService;
use App\Models\Company;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MonthlyUpdateTimeSheets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:monthly-update-timesheets {company_id} {month} {year}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command update-timesheets by company id';

    /**
     * @var Company
     */
    public $timeSheetService;
    public $companyService;
    public $holidayService;
    public $workingDayService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        TimeSheetService $timeSheetService,
        CompanyService $companyService,
        WorkingDayService $workingDayService,
        HolidayService $holidayService
    ) {
        $this->timeSheetService = $timeSheetService;
        $this->companyService = $companyService;
        $this->holidayService = $holidayService;
        $this->workingDayService = $workingDayService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $month = $this->argument('month');
        $year = $this->argument('year');
        $days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $current_day = Carbon::today()->toDateString();
        for ($day = 1; $day <= $days; $day++) {
            $date = $year . '-' . $month . '-' . $day;
            if ($current_day <= $date) {
                $this->updateTimeSheetsByCompanyId($date);
            }
        }

        return 0;
    }

    public function updateTimeSheetsByCompanyId($date)
    {
        $company_id = $this->argument('company_id');
        echo 'START: command:update-timesheets company_id:' . $company_id . ' ' . $date . "\n";
        Log::info('START: command:update-timesheets company_id:' . $company_id);

        if (!$company_id) {
            return;
        }
        $setting = $this->companyService->getSettingOfCompany($company_id);

        if (!$setting) {
            return;
        }
        Log::info('Date:' . $date);

        $holiday = $this->holidayService->checkHolidayOfCompany($company_id, $date);
        $working_day = $this->workingDayService->getWorkingDayConfig($company_id, $date);

        Employee::query()->where(['company_id' => $company_id])
            //            ->where(['id' => 5])
            ->orderBy('id')->chunk(100, function ($employees) use ($working_day, $holiday, $setting, $date) {
                foreach ($employees as $employee) {
                    echo 'employee_code: ' . $employee->employee_code . ', ' . 'employee_id: ' . $employee->id . "\n";
                    Log::info('employee_code: ' . $employee->employee_code . ', ' . 'employee_id: ' . $employee->id);

                    // Nếu là ngày nghỉ lễ
                    if (!$working_day) {
                        $this->timeSheetService->handleWeekendDay($employee, $date);
                        continue;
                    }

                    // Nếu là ngày nghỉ lễ
                    if ($holiday) {
                        $this->timeSheetService->handleHoliday($employee, $date, $working_day, $setting);
                        continue;
                    }

                    // Nếu không là nghĩ lễ và không là cuối tuần
                    $this->timeSheetService->handleNormalDay($employee, $date, $working_day, $setting);
                }
            });
        Log::info('END: command:update-timesheets company_id:' . $company_id);
        echo 'END: command:update-timesheets company_id:' . $company_id . "\n";
    }
}
