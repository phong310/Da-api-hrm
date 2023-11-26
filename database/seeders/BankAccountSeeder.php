<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BankAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('bank_accounts')->insert([
            [
                'employee_id' => 72,
                'account_number' => '123456',
                'account_name' => 'Test bank account name',
                'bank_type' => 'Bank Type',
                'bank_branch' => 'Cầu Giấy',
                'bank_name' => 'BIDV',
            ],
        ]);
    }
}
