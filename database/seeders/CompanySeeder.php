<?php

namespace Database\Seeders;

use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('companies')->insert([
            [
                'name' => 'Solashi',
                'phone_number' => '123456789',
                'tax_code' => '123456789',
                'address' => 'HN',
                'status' => Company::STATUS['ACTIVE'],
                'start_time' => Carbon::now(),
                'representative' => 'Hoàng Đăng Lâm',
                'type_of_business' => 1,
                'end_time' => Carbon::now(),
                'register_date' => Carbon::now(),
            ],
        ]);
    }
}
