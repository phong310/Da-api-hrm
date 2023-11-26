<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersonalInformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('personal_information')->insert([
            [
                'first_name' => 'Admin',
                'last_name' => 'Solashi',
                'job_id' => 1,
                'nickname' => 'Admin Solashi',
                'birthday' => Carbon::now()->format('Y-m-d H:i:s'),
                'marital_status' => 1,
                'sex' => 1,
                'education_level_id' => 1,
                'email' => 'admin_solashi@gmail.com',
                'phone' => '12312312',
                'note' => 'Something to note here',
                'country_id' => 1,
                'ethnic' => 'Kinh',
            ],
            [
                'first_name' => 'Trung',
                'last_name' => 'Vu',
                'job_id' => 1,
                'nickname' => 'Trung',
                'birthday' => Carbon::now()->format('Y-m-d H:i:s'),
                'marital_status' => 1,
                'sex' => 1,
                'education_level_id' => 1,
                'email' => 'trung@gmail.com',
                'phone' => '12312312',
                'note' => 'Something to note here',
                'country_id' => 1,
                'ethnic' => 'Tay',
            ],
        ]);
    }
}
