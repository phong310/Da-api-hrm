<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run($company_id = 1)
    {
        DB::table('m_jobs')->insert([
            [
                'name' => 'IT',
                'company_id' => $company_id,
            ],
            [
                'name' => 'HR',
                'company_id' => $company_id,
            ],
            [
                'name' => 'Accountant',
                'company_id' => $company_id,
            ],
        ]);
    }
}
