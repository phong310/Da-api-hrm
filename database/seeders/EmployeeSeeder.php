<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('employees')->insert([
            [
                'card_number' => '1111',
                'employee_code' => '2',
                'official_employee_date' => Carbon::now(),
                'date_start_work' => Carbon::now(),
                'position_id' => 1,
                'department_id' => 1,
                'company_id' => 1,
                'branch_id' => 1,
                'personal_information_id' => 1,
                'status' => 2,
            ],
            [
                'card_number' => '2222',
                'employee_code' => '1',
                'official_employee_date' => Carbon::now(),
                'date_start_work' => Carbon::now(),
                'position_id' => 1,
                'department_id' => 1,
                'company_id' => 1,
                'branch_id' => 1,
                'personal_information_id' => 2,
                'status' => 2,
            ],
        ]);
    }
}
