<?php

namespace Database\Seeders\Backend\FrontWeb;

use App\Models\Backend\FrontWeb\WhyCourier;
use App\Models\Backend\Upload;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder; 
class WhyCourierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
     

        $lists = [
            'Many years experience in deliver booked consignment safely.'      =>'timly-delivery.png',
            'We keep a tight eye on the vehicle until it reaches its destination.'      =>'limitless-pickup.png', 
            'Online Tracking System keeps you updated with the current status.'=>'cash-on-delivery.png', 
            'High levels of service and the ability to offer personal attention to clients.' =>'payment.png',
            'We are fast, We are smart and We are reliable.'      =>'handling.png',
            'Trusted Logistics Partners for many big companies'  =>'live-tracking.png',  
            'Our mission is to exceed customer`s expectations by providing the most reliable service, innovative solutions, technology and professionalism.'  =>'live-tracking.png',  
        ]; 
        $i = 0;
        foreach ($lists as  $key=>$item) {   
            $i++;        
            $upload           = new Upload();
            $upload->original = "frontend/images/whycourier/".$item;
            $upload->save(); 

            $whycourier             = new WhyCourier();
            $whycourier->content    = $key;  
            $whycourier->icon       = 'fa fa-circle-check';  
            $whycourier->position   = $i;
            $whycourier->save(); 
        }
 
    }
}
