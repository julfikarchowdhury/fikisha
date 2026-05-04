<?php

namespace Database\Seeders\Backend\FrontWeb;
 
use App\Models\Backend\FrontWeb\Service;
use App\Models\Backend\ShippingType;
use App\Models\Backend\Upload;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $inside_shipping_types = ShippingType::where('delivery_type_id',1)->get();
        foreach ($inside_shipping_types as $key => $inside) { 
            $upload           = new Upload();
            $nu = $key+1;
            $upload->original = "frontend/images/services/inside_".$nu.".png";
            $upload->save(); 
            
            $service                    = new Service(); 
            $service->delivery_type_id  = $inside->delivery_type_id; 
            $service->shipping_type_id  = $inside->id; 
            $service->image_id          = $upload->id; 
            $service->description       = $faker->sentence(50);
            $service->position          = $key+1;
            $service->save(); 
        }
        
        $outside_shipping_types = ShippingType::where('delivery_type_id',3)->get();
        foreach ($outside_shipping_types as $outkey => $outside) { 
            $upload           = new Upload();
            $num = $outkey+1;
            $upload->original = "frontend/images/services/outside_".$num.".png";
            $upload->save(); 
            
            $service                    = new Service(); 
            $service->delivery_type_id  = $outside->delivery_type_id; 
            $service->shipping_type_id  = $outside->id; 
            $service->image_id          = $upload->id; 
            $service->description       = $faker->sentence(50);
            $service->position          = $outkey+1;
            $service->save(); 
        }
         
    }
}
