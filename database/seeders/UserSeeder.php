<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'user_name' => 'employee_solashi',
                'email' => 'employee_solashi@hrm.com',
                'password' => bcrypt('admin@123'),
                'company_id' => 1,
                'employee_id' => 2,
            ]
        ]);
    }
}
