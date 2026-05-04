<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Backend\DeliveryCharge;
use App\Enums\Status;
use Illuminate\Support\Facades\DB;

class DeliveryChargeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       DB::statement("INSERT INTO `delivery_charges` (`id`, `shipping_type`, `delivery_type_id`, `from_country_id`, `from_city_id`, `from_district_id`, `from_town_id`, `from_portal_code`, `to_country_id`, `to_city_id`, `to_district_id`, `to_town_id`, `to_portal_code`, `dtd_start_weight`, `dtd_end_weight`, `dtd_s_e_rate`, `dtd_additional_weight`, `dtd_additional_rate`, `dth_start_weight`, `dth_end_weight`, `dth_s_e_rate`, `dth_additional_weight`, `dth_additional_rate`, `hth_start_weight`, `hth_end_weight`, `hth_s_e_rate`, `hth_additional_weight`, `hth_additional_rate`, `htd_start_weight`, `htd_end_weight`, `htd_s_e_rate`, `htd_additional_weight`, `htd_additional_rate`, `position`, `status`, `created_at`, `updated_at`) VALUES(1, 1, 1, 19, 1, 1, 2, '3912', 19, 1, 1, 1, '3910', 1, 50, '100.00', '0.50', '5.00', 1, 50, '120.00', '0.50', '5.00', 1, 50, '150.00', '0.50', '5.00', 1, 50, '200.00', '0.50', '5.00', 1, 1, '2023-09-10 10:05:56', '2023-09-10 12:24:05');");
        
    }
}
