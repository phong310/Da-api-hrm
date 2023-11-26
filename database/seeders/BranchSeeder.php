<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('m_branches')->insert([
            [
                'name' => 'Chi nhánh Hà Nội',
                'company_id' => 1,
            ],
        ]);
    }
}
