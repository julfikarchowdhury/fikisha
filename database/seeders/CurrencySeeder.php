<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('currencies')->updateOrInsert(
            ['id' => 1],
            [
                'country' => 'Kenya',
                'name' => 'Kenyan shilling',
                'code' => 'KES',
                'symbol' => 'KSh',
                'exchange_rate' => null,
                'status' => 1,
                'position' => null,
                'created_at' => '2022-12-14 08:30:09',
                'updated_at' => '2022-12-14 08:30:09',
            ]
        );
    }
}
