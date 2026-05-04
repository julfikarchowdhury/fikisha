<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Backend\Hub;
use Illuminate\Support\Facades\DB;

class HubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement("INSERT INTO `hubs` (`id`, `name`, `province_id`, `hub_type`, `city_id`, `postal_code`, `town_street_address`, `address`, `phone`, `whatsapp_number`, `building_address`, `email`, `location`, `location_lat`, `location_long`, `current_balance`, `hub_lat`, `hub_long`, `status`, `created_at`, `updated_at`) VALUES
        (1, 'Mirpur-10', 1, 1, 1, 1216, 'Dhaka 1216, Bangladesh', 'house 12 road 03, Dhaka 1216, Bangladesh', '01000000001', '01000000001', 'house 12 road 03', 'demo1@gmail.com', 'Mirpur 10 Roundabout, Dhaka, Bangladesh', '23.8069245', '90.36869779999999', '0.00', '23.8069245', '90.36869779999999', 1, '2023-10-08 18:47:27', '2024-03-05 03:00:08'),
        (2, 'Mirpur - 12', 2, 1, 2, 1216, 'Dhaka 1216, Bangladesh', 'house 12 road 03, Dhaka 1216, Bangladesh', '01745214578', '01745214578', 'house 12 road 03', 'demo2@gmail.com', '12 Mirpur Road, Dhaka, Bangladesh', '23.8217924', '90.3696587', '0.00', '23.8217924', '90.3696587', 1, '2024-01-28 00:34:52', '2024-03-05 03:00:25');");
        
        DB::statement("INSERT INTO `hub_shipping_types` (`id`, `province_id`, `delivery_type_id`, `hub_id`, `shipping_type_id`, `title`, `status`, `created_at`, `updated_at`) VALUES
        (1, 2, 1, 2, 1, 'Door to Door', 1, '2024-03-09 06:12:26', '2024-03-09 06:12:26'),
        (2, 2, 1, 2, 2, 'Door to hub', 1, '2024-03-09 06:12:26', '2024-03-09 06:12:26'),
        (3, 2, 1, 2, 3, 'Hub to door', 1, '2024-03-09 06:12:26', '2024-03-09 06:12:26'),
        (4, 2, 1, 2, 4, 'Hub to Hub', 1, '2024-03-09 06:12:26', '2024-03-09 06:12:26'),
        (5, 2, 3, 2, 5, 'Door to Door', 1, '2024-03-09 06:12:26', '2024-03-09 06:12:26'),
        (6, 2, 3, 2, 6, 'Door to hub', 1, '2024-03-09 06:12:26', '2024-03-09 06:12:26'),
        (7, 2, 3, 2, 7, 'Hub to door', 1, '2024-03-09 06:12:26', '2024-03-09 06:12:26'),
        (8, 2, 3, 2, 8, 'Hub to Hub', 1, '2024-03-09 06:12:26', '2024-03-09 06:12:26'),
        (9, 1, 1, 1, 1, 'Door to Door', 1, '2024-03-09 06:12:43', '2024-03-09 06:12:43'),
        (10, 1, 1, 1, 2, 'Door to hub', 1, '2024-03-09 06:12:43', '2024-03-09 06:12:43'),
        (11, 1, 1, 1, 3, 'Hub to door', 1, '2024-03-09 06:12:43', '2024-03-09 06:12:43'),
        (12, 1, 1, 1, 4, 'Hub to Hub', 1, '2024-03-09 06:12:43', '2024-03-09 06:12:43'),
        (13, 1, 3, 1, 5, 'Door to Door', 1, '2024-03-09 06:12:43', '2024-03-09 06:12:43'),
        (14, 1, 3, 1, 6, 'Door to hub', 1, '2024-03-09 06:12:43', '2024-03-09 06:12:43'),
        (15, 1, 3, 1, 7, 'Hub to door', 1, '2024-03-09 06:12:43', '2024-03-09 06:12:43'),
        (16, 1, 3, 1, 8, 'Hub to Hub', 1, '2024-03-09 06:12:43', '2024-03-09 06:12:43');");
    }
}
