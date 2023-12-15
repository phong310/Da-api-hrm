<?php

namespace App\Console\Commands;

use App\Http\Services\v1\Admin\CompanyService;
use App\Http\Services\v1\Admin\CompensatoryWorkingDayService;
use App\Http\Services\v1\Admin\HolidayService;
use App\Http\Services\v1\Admin\TimeSheetService;
use App\Http\Services\v1\Admin\WorkingDayService;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateTimeSheets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update-timesheets {company_id} {yesterday}';

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
    public $compensatoryWorkingDayService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        TimeSheetService $timeSheetService,
        CompanyService $companyService,
        WorkingDayService $workingDayService,
        HolidayService $holidayService,
        CompensatoryWorkingDayService $compensatoryWorkingDayService
    ) {
        $this->timeSheetService = $timeSheetService;
        $this->companyService = $companyService;
        $this->holidayService = $holidayService;
        $this->workingDayService = $workingDayService;
        $this->compensatoryWorkingDayService = $compensatoryWorkingDayService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->updateTimeSheetsByCompanyId();

        return 0;
    }

    public function updateTimeSheetsByCompanyId()
    {
        $company_id = $this->argument('company_id');
        $yesterday = $this->argument('yesterday');
        echo 'START: command:update-timesheets company_id:' . $company_id . ' ' . $yesterday . "\n";
        Log::info('START: command:update-timesheets company_id:' . $company_id);

        if (!$company_id) {
            return;
        }
        $setting = $this->companyService->getSettingOfCompany($company_id);

        if (!$setting) {
            return;
        }
        Log::info('YESTERDAY:' . $yesterday);

        $holiday = $this->holidayService->checkHolidayOfCompany($company_id, $yesterday);
        $working_day = $this->workingDayService->getWorkingDayConfig($company_id, $yesterday);
        //        $compensatoryWorkingDay = $this->compensatoryWorkingDayService->checkCompensatoryWorkingDayOfCompany($company_id, $yesterday);
        Employee::query()->where(['company_id' => $company_id])
            ->orderBy('id')->chunk(Employee::QUANTITY_EMPLOYEE['QUANTITY'], function ($employees) use ($working_day, $holiday, $setting, $yesterday) {
                foreach ($employees as $employee) {
                    echo 'employee_code: ' . $employee->employee_code . ', ' . 'employee_id: ' . $employee->id . "\n";
                    Log::info('employee_code: ' . $employee->employee_code . ', ' . 'employee_id: ' . $employee->id);

                    // TODO: Nếu ngày đó là ngày làm việc bù
                    //                    if ($compensatoryWorkingDay) {
                    //                        $this->timeSheetService->handleCompensatoryWorkingDay($employee, $yesterday, $compensatoryWorkingDay, $setting);
                    //                        continue;
                    //                    }

                    // TODO: Nếu là ngày không làm việc
                    if (!$working_day) {
                        $this->timeSheetService->handleWeekendDay($employee, $yesterday);
                        continue;
                    }

                    // TODO: Nếu là ngày nghỉ lễ
                    if ($holiday) {
                        $this->timeSheetService->handleHoliday($employee, $yesterday, $working_day, $setting);
                        continue;
                    }

                    // TODO: Ngày làm việc bình thường
                    // TODO: Nếu không là nghĩ lễ và không là cuối tuần và không là ngày làm bù
                    $this->timeSheetService->handleNormalDay($employee, $yesterday, $working_day, $setting);
                }
            });
        Log::info('END: command:update-timesheets company_id:' . $company_id);
        echo 'END: command:update-timesheets company_id:' . $company_id . "\n";
    }
}
