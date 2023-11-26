<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DaysInWeekSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('days_in_week')->insert([
            [
                'name' => 'Monday',
                'symbol' => 'Mo',
            ],
            [
                'name' => 'Tuesday',
                'symbol' => 'Tue',
            ],
            [
                'name' => 'Wednesday',
                'symbol' => 'Wed',
            ],
            [
                'name' => 'Thursday',
                'symbol' => 'Thur',
            ],
            [
                'name' => 'Friday',
                'symbol' => 'Fri',
            ],
            [
                'name' => 'Saturday',
                'symbol' => 'Sat',
            ],
            [
                'name' => 'Sunday',
                'symbol' => 'Sun',
            ],
        ]);
    }
}
