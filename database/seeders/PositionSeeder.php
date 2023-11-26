<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run($company_id = 1)
    {
        DB::table('m_positions')->insert([
            [
                'name' => 'Thực tập sinh',
                'company_id' => $company_id,
            ],
            [
                'name' => 'Nhân viên chính thức',
                'company_id' => $company_id,
            ],
            [
                'name' => 'Nhân viên thử việc',
                'company_id' => $company_id,
            ],
        ]);
    }
}
