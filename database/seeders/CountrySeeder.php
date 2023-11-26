<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('m_countries')->insert([
            [
                'name' => 'Việt Nam',
            ],
            [
                'name' => 'Trung Quốc',
            ],
            [
                'name' => 'Thái Lan',
            ],
            [
                'name' => 'Nhật Bản',
            ],
        ]);
    }
}
