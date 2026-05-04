<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\Config;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $config = [
            'fragile_liquid_status'             => Status::ACTIVE,
            'fragile_liquid_charge'             => 100,
            'fragile_liquid_outside_charge'     => 200,
            
            'rush_hour_service_status'          => Status::ACTIVE,
            'rush_hour_service_charge'          => 100,
            'rush_hour_service_outside_charge'  => 200,

            'scheduled_service_status'          => Status::ACTIVE,
            'scheduled_service_charge'          => 100,
            'scheduled_service_outside_charge'  => 200,

            'same_day'                          => 1,
            'next_day'                          => 0,
            'sub_city'                          => 0,
            'outside_City'                      => 1
        ];
        foreach ($config as $key => $value) {
             $confg        = new Config();
             $confg->key   = $key;
             $confg->value = $value;
             $confg->save();
        }
    }
}
