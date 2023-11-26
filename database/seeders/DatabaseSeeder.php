<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call([
            CompanySeeder::class,
            DepartmentSeeder::class,
            SettingSeeder::class,
            JobSeeder::class,
            CountrySeeder::class,
            PeopleSeeder::class,
            EducationLevelSeeder::class,
            TitleSeeder::class,
            PositionSeeder::class,
            KindOfLeaveSeeder::class,
            HolidaySeeder::class,
            DaysInWeekSeeder::class,
            WorkingDaySeeder::class,
            BranchSeeder::class,
            EmployeeSeeder::class,
            PersonalInformationSeeder::class,
        ]);
    }
}
