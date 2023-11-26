<?php

namespace Database\Seeders;

use App\Models\Master\WorkingDay;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkingDaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('m_working_day')->insert([
            [
                'name' => 'Monday',
                'type' => WorkingDay::TYPE['OFFICE_HOURS'],
                'start_time' => '09:00',
                'start_lunch_break' => '12:00',
                'end_lunch_break' => '13:00',
                'end_time' => '18:00',
                'day_in_week_id' => 1,
                'company_id' => 1,
            ],
            [
                'name' => 'Tuesday',
                'type' =>  WorkingDay::TYPE['OFFICE_HOURS'],
                'start_time' => '09:00',
                'end_time' => '18:00',
                'start_lunch_break' => '12:00',
                'end_lunch_break' => '13:00',
                'day_in_week_id' => 2,
                'company_id' => 1,
            ],
            [
                'name' => 'Wednesday',
                'type' =>  WorkingDay::TYPE['OFFICE_HOURS'],
                'start_time' => '09:00',
                'end_time' => '18:00',
                'start_lunch_break' => '12:00',
                'end_lunch_break' => '13:00',
                'day_in_week_id' => 3,
                'company_id' => 1,
            ],
            [
                'name' => 'Thursday',
                'type' =>  WorkingDay::TYPE['OFFICE_HOURS'],
                'start_time' => '09:00',
                'end_time' => '18:00',
                'start_lunch_break' => '12:00',
                'end_lunch_break' => '13:00',
                'day_in_week_id' => 4,
                'company_id' => 1,
            ],
            [
                'name' => 'Friday',
                'type' =>  WorkingDay::TYPE['OFFICE_HOURS'],
                'start_time' => '09:00',
                'end_time' => '18:00',
                'start_lunch_break' => '12:00',
                'end_lunch_break' => '13:00',
                'day_in_week_id' => 5,
                'company_id' => 1,
            ],
        ]);
    }
}
