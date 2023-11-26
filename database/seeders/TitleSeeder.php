<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TitleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('m_titles')->insert([
            [
                'name' => 'Development',
                'company_id' => 1,
            ],
            [
                'name' => 'HR',
                'company_id' => 1,
            ],
            [
                'name' => 'Leader',
                'company_id' => 1,
            ],
            [
                'name' => 'CTO',
                'company_id' => 1,
            ],
        ]);
    }
}
