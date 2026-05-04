<?php

namespace Database\Seeders;

use App\Models\Backend\DeliveryType;
use App\Models\Backend\ShippingChargeOption;
use App\Models\Backend\ShippingType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShippingTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
     
        //inside city
        DB::statement("INSERT INTO `shipping_types` (`id`, `delivery_type_id`, `title`, `basic_price`, `start_weight`, `end_weight`, `addi_weight_price`, `start_volume`, `end_volume`, `addi_volume_price`, `slots`, `created_at`, `updated_at`) VALUES
        (1, 1, 'Door to Door', '325.00', '0.00', '5.00', '100.00', '0.000', '0.036', '100.00', '1-2 hours',  '2023-10-09 12:49:19', '2023-10-09 12:49:19'),
        (2, 1, 'Door to hub', '325.00', '0.00', '5.00', '100.00', '0.000', '0.036', '100.00', '1-2 hours', '2023-10-09 12:50:16', '2023-10-09 12:50:16'),
        (3, 1, 'Hub to door', '325.00', '0.00', '5.00', '100.00', '0.000', '0.036', '100.00', '1-2 hours', '2023-10-09 12:50:16', '2023-10-09 12:50:16'),
        (4, 1, 'Hub to Hub', '325.00', '0.00', '5.00', '100.00', '0.000', '0.036', '100.00', '1-2 hours', '2023-10-09 12:50:16', '2023-10-09 12:50:16');");
   
        //outside city
        DB::statement("INSERT INTO `shipping_types` (`id`, `delivery_type_id`, `title`, `basic_price`, `start_weight`, `end_weight`, `addi_weight_price`, `start_volume`, `end_volume`, `addi_volume_price`, `slots`, `created_at`, `updated_at`) VALUES
        (5, 3, 'Door to Door', '325.00', '0.00', '5.00', '200.00', '0.000', '0.036', '200.00', '6-12 Hours',  '2023-10-09 12:49:19', '2023-10-09 12:49:19'),
        (6, 3, 'Door to hub', '400.00', '0.00', '5.00', '200.00', '0.000', '0.036', '200.00',  '24H',  '2023-10-09 12:50:16', '2023-10-09 12:50:16'),
        (7, 3, 'Hub to door', '425.00', '0.00', '5.00', '200.00', '0.000', '0.036', '200.00',  '48H',  '2023-10-09 12:50:16', '2023-10-09 12:50:16'),
        (8, 3, 'Hub to Hub', '450.00', '0.00', '5.00', '200.00', '0.000', '0.036', '200.00',  '72H',   '2023-10-09 12:50:16', '2023-10-09 12:50:16');");
    
        $outsidecity_charge = [
           
            [
                'from_km' => 60,
                'to_km'   => 150,
                'basic_price'   => 600
            ],
            [
                'from_km'    => 150,
                'to_km'      => 300,
                'basic_price'      => 700
            ],
            [
                'from_km'    => 300,
                'to_km'      => 500,
                'basic_price'   => 800
            ],
            [
                'from_km'  => 500,
                'to_km'    => 700,
                'basic_price' => 900  
            ],
            [
                'from_km'  => 700,
                'to_km'    => 1000,
                'basic_price' => 900
            ],
            [
                'from_km'  => 1000,
                'to_km'    => 1300,
                'basic_price' => 1150
            ],
            [
                'from_km'  => 1300,
                'to_km'    => 1800,
                'basic_price' =>1300
            ]
        ];
        $outsideCity = ShippingType::where('delivery_type_id',3)->get();
        foreach ($outsideCity as $key => $shipType) {
            foreach ($outsidecity_charge as $key => $option) {  
                $option = (object)$option;  
                ShippingChargeOption::create([
                    'shipping_type_id' => $shipType->id,
                    'from_km'          => $option->from_km,
                    'to_km'            => $option->to_km,
                    'basic_price'      => $option->basic_price 
                ]);
            }
        }
                
    }
}
