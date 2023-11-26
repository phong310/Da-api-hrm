<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('addresses')->insert([
            [
                'province' => 'Hà Nội',
                'district' => 'Cầu Giấy',
                'ward' => 'Trần Thái Tông',
                'address' => '35/45',
                'type' => 0,
                'personal_information_id' => 1,
            ],
        ]);
    }
}
