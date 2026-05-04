<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Backend\Upload;
use Illuminate\Support\Facades\DB;

class UploadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user           = new Upload();
        $user->original = "uploads/users/user.png";
        $user->save();

        $user           = new Upload();
        $user->original = "uploads/users/user2.png";
        $user->save();

        $user           = new Upload();
        $user->original = "uploads/users/user3.png";
        $user->save();

        $user           = new Upload();
        $user->original = "uploads/users/user4.png";
        $user->save();

        $user           = new Upload();
        $user->original = "uploads/users/user5.png";
        $user->save();
       
        $user           = new Upload();
        $user->original = "uploads/users/user6.png";
        $user->save();
       
        $user           = new Upload();
        $user->original = "uploads/users/user7.png";
        $user->save();
        

        DB::statement("INSERT INTO `uploads` (`id`, `original`, `one`, `two`, `three`, `created_at`, `updated_at`) VALUES
        (8, 'uploads/slider/slider-1.png', NULL, NULL, NULL, '2023-11-27 02:53:51', '2023-11-27 02:53:51'),
        (9, 'uploads/slider/moving.gif', NULL, NULL, NULL, '2023-11-27 02:53:51', '2023-11-27 02:53:51'),
        (10, 'uploads/slider/dropped.png', NULL, NULL, NULL, '2023-11-27 02:53:51', '2023-11-27 02:53:51');");

        //our work process image
        DB::statement("INSERT INTO `uploads` (`id`, `original`, `one`, `two`, `three`, `created_at`, `updated_at`) VALUES
        (11, 'uploads/section/our-work-process/1.png', NULL, NULL, NULL, '2023-11-27 02:53:51', '2023-11-27 02:53:51'),
        (12, 'uploads/section/our-work-process/2.png', NULL, NULL, NULL, '2023-11-27 02:53:51', '2023-11-27 02:53:51'),
        (13, 'uploads/section/our-work-process/3.png', NULL, NULL, NULL, '2023-11-27 02:53:51', '2023-11-27 02:53:51'),
        (14, 'uploads/section/our-work-process/4.png', NULL, NULL, NULL, '2023-11-27 02:53:51', '2023-11-27 02:53:51'),
        (15, 'uploads/section/our-work-process/5.png', NULL, NULL, NULL, '2023-11-27 02:53:51', '2023-11-27 02:53:51'),
        (16, 'uploads/section/why-choose-us.jpg', NULL, NULL, NULL, '2023-11-27 02:53:51', '2023-11-27 02:53:51'),
        (17, 'uploads/section/service-banner.png', NULL, NULL, NULL, '2023-11-27 02:53:51', '2023-11-27 02:53:51'),
        (18, 'uploads/section/banner.png', NULL, NULL, NULL, '2023-11-27 02:53:51', '2023-11-27 02:53:51');");

  
    }
}
