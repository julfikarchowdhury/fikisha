<?php

namespace Database\Seeders;

use App\Models\Backend\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Province::truncate();
 
        DB::statement("INSERT INTO `provinces` ( `name`, `province_code`, `position`, `description`, `status`, `created_at`, `updated_at`)
                    VALUES ( 'Nairobi', 'KE-NAI', 0, NULL, 1, NULL, NULL),
                        ( 'Mombasa', 'KE-MBA', 0, NULL, 1, NULL, NULL),
                        ( 'Kisumu', 'KE-KSM', 0, NULL, 1, NULL, NULL),
                        ( 'Nakuru', 'KE-NKR', 0, NULL, 1, NULL, NULL),
                        ( 'Uasin Gishu', 'KE-UG', 0, NULL, 1, NULL, NULL),
                        ( 'Dhaka', 'BD-DHA', 0, NULL, 1, NULL, NULL),
                        ( 'Chattogram', 'BD-CTG', 0, NULL, 1, NULL, NULL),
                        ( 'Khulna', 'BD-KHU', 0, NULL, 1, NULL, NULL),
                        ( 'Rajshahi', 'BD-RAJ', 0, NULL, 1, NULL, NULL),
                        ( 'Sylhet', 'BD-SYL', 0, NULL, 1, NULL, NULL);");
    }
}
