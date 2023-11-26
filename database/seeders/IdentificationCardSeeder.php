<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IdentificationCardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('identification_cards')->insert([
            [
                'ID_no' => '1',
                'issued_date' => Carbon::now()->format('Y-m-d H:i:s'),
                'issued_by' => 'Don\'t know',
                'ID_expire' => '1',
                'personal_information_id' => 1,
                'type' => 0,
            ],
        ]);
    }
}
 