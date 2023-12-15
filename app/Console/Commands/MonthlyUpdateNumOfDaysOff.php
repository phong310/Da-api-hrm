<?php

namespace App\Console\Commands;

use App\Http\Services\v1\Admin\CompanyService;
use App\Http\Services\v1\Admin\NumberOfDaysOffService;
use App\Models\Employee;
use App\Models\Setting\SettingLeaveDay;
use App\Repositories\Interfaces\SettingLeaveDayInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class MonthlyUpdateNumOfDaysOff extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:monthly-update-numberOfDaysOff {company_id} {month} {year}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command update-number of days off by company id';
    /**
     * @var SettingLeaveDayInterface
     */
    private $settingLeaveDay;
    /**
     * @var NumberOfDaysOffService
     */
    private $numberOfDaysOffService;
    /**
     * @var CompanyService
     */
    private $companyService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        SettingLeaveDayInterface $settingLeaveDay,
        NumberOfDaysOffService $numberOfDaysOffService,
        CompanyService $companyService
    ) {
        $this->settingLeaveDay = $settingLeaveDay;
        $this->numberOfDaysOffService = $numberOfDaysOffService;
        $this->companyService = $companyService;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->updateNumberOfDaysOffByCompanyId();

        return 0;
    }

    public function updateNumberOfDaysOffByCompanyId()
    {
        $company_id = $this->argument('company_id');
        $year = $this->argument('year');
        $month = $this->argument('month');
        Log::info('START: command:monthly-update-numberOfDaysOff ' . $company_id . ' ' . $month . ' ' . $year);
        echo 'START: command:monthly-update-numberOfDaysOff ' . $company_id . ' ' . $month . ' ' . $year . "\n";

        if (!$company_id) {
            return;
        }
        $setting = $this->companyService->getSettingOfCompany($company_id);

        if (!$setting) {
            return;
        }

        $date = Carbon::create($year, $month);
        $settingLeaveDayAnnual = $this->settingLeaveDay->showByType($company_id, SettingLeaveDay::TYPE['ANNUAL']);
        if (!$settingLeaveDayAnnual) {
            return;
        }

        Employee::query()->where(['company_id' => $company_id])
            ->orderBy('id')->chunk(100, function ($employees) use ($company_id, $date, $settingLeaveDayAnnual) {
                foreach ($employees as $employee) {
                    echo 'employee_code: ' . $employee->employee_code . ', ' . 'employee_id: ' . $employee->id . "\n";
                    Log::info('employee_code: ' . $employee->employee_code . ', ' . 'employee_id: ' . $employee->id);

                    // Nếu là vị trí có ngày nghỉ hằng năm
                    if ($this->settingLeaveDay->checkPositionHasLeaveDay($company_id, $employee->position_id, $date, SettingLeaveDay::TYPE['ANNUAL'])) {
                        $this->numberOfDaysOffService->handleUpdateMonthly($employee, $settingLeaveDayAnnual, $date);
                    }
                }
            });

        Log::info('END: command:monthly-update-numberOfDaysOff ' . $company_id . ' ' . $month . ' ' . $year);
        echo 'END: command:monthly-update-numberOfDaysOff ' . $company_id . ' ' . $month . ' ' . $year . "\n";
    }
}
