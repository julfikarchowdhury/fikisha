<?php

namespace Database\Seeders\Backend\FrontWeb;

use App\Models\Backend\FrontWeb\Partner;
use App\Models\Backend\Upload;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker  = Faker::create('en_US');
        $data = [
            '1.jpg',
            '2.png',
            '3.jpg',
            '4.jpg',
            '5.jpg',
            '1.jpg',
            '2.png',
            '3.jpg',
            '4.jpg',
            '5.jpg',
            '1.jpg',
            '2.png', 
        ]; 
      foreach ($data as $key => $value) { 

        $upload           = new Upload();
        $upload->original = "frontend/images/partner/".$value;
        $upload->save(); 
        $partner           = new Partner();
        $partner->name     = $faker->unique()->company();
        $partner->image_id = $upload->id;
        $partner->link     = '#';
        $partner->position = ($key+1);
        $partner->save(); 
      } 
       
    }
}
