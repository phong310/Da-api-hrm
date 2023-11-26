<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            'format_date' => 'yyyy-MM-dd',
            'locale' => 'en',
            'time_zone' => 'UTC',
            'company_id' => 1,
        ]);
    }
}
