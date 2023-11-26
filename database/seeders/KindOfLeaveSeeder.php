<?php

namespace Database\Seeders;

use App\Models\Master\KindOfLeave;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KindOfLeaveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run($company_id = 1)
    {
        DB::table('kind_of_leave')->insert([
            [
                'name' => 'Nghỉ ốm',
                'symbol' => 'O',
                'type' => KindOfLeave::TYPE['NORMAL_LEAVE'],
                'company_id' => $company_id,
            ],
            [
                'name' => 'Nghỉ con ốm',
                'symbol' => 'CO',
                'type' => KindOfLeave::TYPE['NORMAL_LEAVE'],
                'company_id' => $company_id,
            ],
            [
                'name' => 'Nghỉ ra ngoài có việc riêng',
                'symbol' => 'VR',
                'type' => KindOfLeave::TYPE['NORMAL_LEAVE'],
                'company_id' => $company_id,
            ],

            [
                'name' => 'Nghỉ sinh lý',
                'symbol' => 'SL',
                'type' => KindOfLeave::TYPE['COMPENSATORY_LEAVE'],
                'company_id' => $company_id,
            ],
            [
                'name' => 'Nghỉ bù tăng ca',
                'symbol' => 'NBTC',
                'type' => KindOfLeave::TYPE['COMPENSATORY_LEAVE'],
                'company_id' => $company_id,
            ],
        ]);
    }
}
