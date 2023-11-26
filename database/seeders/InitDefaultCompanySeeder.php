<?php

namespace Database\Seeders;

use App\Models\Master\Holiday;
use App\Models\Master\Job;
use App\Models\Master\KindOfLeave;
use App\Models\Master\Position;
// use App\Models\Setting\SettingTypeOvertime;
use Illuminate\Database\Seeder;

class InitDefaultCompanySeeder extends Seeder
{
    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //        $this->call(CountrySeeder::class);
        //        $this->call(DaysInWeekSeeder::class);
        //        $this->call(EducationLevelSeeder::class);
        //        $this->call(PeopleSeeder::class);
        //        $this->call(PositionSeeder::class);
        //        $this->call(RegionSeeder::class);

        $this->runSeeder(Holiday::query(), HolidaySeeder::class);
        $this->runSeeder(KindOfLeave::query(), KindOfLeaveSeeder::class);
        $this->runSeeder(Job::query(), JobSeeder::class);
        $this->runSeeder(Position::query(), PositionSeeder::class);
        // $this->runSeeder(SettingTypeOvertime::query(), SettingTypeOvertimeSeeder::class);
    }

    public function runSeeder($query, $seederClass)
    {
        $q = $query->where(['company_id' => $this->company_id]);
        if (!$q->exists()) {
            $this->callWith($seederClass, [$this->company_id]);
        }
    }
}
