<?php

namespace Database\Seeders;

use App\Models\Master\Holiday;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run($company_id = 1)
    {
        DB::table('holidays')->insert([
            [
                'name' => 'Tết dương lịch',
                'start_date' => '2022-01-01',
                'end_date' => '2022-01-01',
                'type' => Holiday::TYPE['ANNUAL'],
                'company_id' => $company_id,
            ],
            [
                'name' => 'Quốc tế lao động',
                'start_date' => '2022-05-01',
                'end_date' => '2022-05-01',
                'type' => Holiday::TYPE['ANNUAL'],
                'company_id' => $company_id,
            ],
            [
                'name' => 'Quốc khánh',
                'start_date' => '2022-09-02',
                'end_date' => '2022-09-02',
                'type' => Holiday::TYPE['ANNUAL'],
                'company_id' => $company_id,
            ],
        ]);
    }
}
