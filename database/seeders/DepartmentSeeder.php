<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('m_departments')->insert([
            [
                'name' => 'Kỹ thuật',
                'company_id' => 1,
            ],
            [
                'name' => 'Kế toán',
                'company_id' => 1,
            ],
        ]);
    }
}
