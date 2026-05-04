<?php

namespace Database\Seeders\Backend\FrontWeb;

use App\Models\Backend\Upload;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
          

        DB::statement("INSERT INTO `sections` ( `type`, `key`, `value`, `created_at`, `updated_at`) VALUES
            ( 1, 'banner',17, '2023-01-27 17:30:40', '2023-01-27 17:30:40'),
            ( 7, 'title','Why choose US ?', '2023-01-27 17:30:40', '2023-01-27 17:30:40'),
            ( 7, 'banner',16, '2023-01-27 17:30:40', '2023-01-27 17:30:40'),
            ( 8, 'banner',18, '2023-01-27 17:30:40', '2023-01-27 17:30:40'),
            -- contact
            ( 9, 'address','99 NY Address street, Brooklyn, United State', '2023-01-27 17:30:40', '2023-01-27 17:30:40'),
            ( 9, 'phone','875 7556 464 765 8 765 648 567 98', '2023-01-27 17:30:40', '2023-01-27 17:30:40'),
            ( 9, 'email','example@gmail.com', '2023-01-27 17:30:40', '2023-01-27 17:30:40'),
            ( 9, 'website','example.com', '2023-01-27 17:30:40', '2023-01-27 17:30:40'),
            -- end contact

            ( 2, 'title','Our work process', '2023-01-27 17:30:40', '2023-01-27 17:30:40'), 
            ( 2, 'description','Our Work Process is very simple as mentioned below but very strict when it comes to the safe delivery of the consignment.', '2023-01-27 17:30:40', '2023-01-27 17:30:40'), 
            ( 2, 'icon_1','11', '2023-01-27 17:30:40', '2023-01-27 17:30:40'), 
            ( 2, 'title_1','Book Consignment', '2023-01-27 17:30:40', '2023-01-27 17:30:40'),
            ( 2, 'icon_2','12', '2023-01-27 17:30:40', '2023-01-27 17:30:40'), 
            ( 2, 'title_2','Pack Goods', '2023-01-27 17:30:40', '2023-01-27 17:30:40'),
            ( 2, 'icon_3','13', '2023-01-27 17:30:40', '2023-01-27 17:30:40'), 
            ( 2, 'title_3','Safe Loading', '2023-01-27 17:30:40', '2023-01-27 17:30:40'),
            ( 2, 'icon_4','14', '2023-01-27 17:30:40', '2023-01-27 17:30:40'), 
            ( 2, 'title_4','Transportation', '2023-01-27 17:30:40', '2023-01-27 17:30:40'),
            ( 2, 'icon_5','15', '2023-01-27 17:30:40', '2023-01-27 17:30:40'), 
            ( 2, 'title_5','Safe Delivery', '2023-01-27 17:30:40', '2023-01-27 17:30:40'),
           
            ( 3, 'about_us', 'Fastest platform with all courier service features. Help you start, run and grow your courier service.', '2023-01-27 17:30:40', '2023-01-27 17:30:40'),
            ( 4, 'subscribe_title', 'Subscribe Us', '2023-01-27 17:30:40', '2023-01-27 17:30:40'),
            ( 4, 'subscribe_description','Get business news , tip and solutions to your problems our experts.', '2023-01-27 17:30:40', '2023-01-27 17:30:40'),
            ( 5, 'playstore_link','#', '2023-01-27 17:30:40', '2023-01-27 17:30:40'),
            ( 5, 'ios_link','#', '2023-01-27 17:30:40', '2023-01-27 17:30:40'),
            ( 6, 'map_link','https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3198.1208295794127!2d3.182278026293642!3d36.71965922223464!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x128e51f31a4c4da7%3A0xcac04c98846b5b62!2sCit%C3%A9%20EPLF%2C1080%20Logts%2C%20Bab%20Ezzouar%2C%20Algeria!5e0!3m2!1sen!2sbd!4v1715660201493!5m2!1sen!2sb', '2023-01-27 17:30:40', '2023-01-27 17:30:40')");
    }
}
