<?php

namespace Database\Seeders\Backend\FrontWeb;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement("INSERT INTO `sliders` (`id`, `title`, `slider`, `small_title`, `status`, `position`, `created_at`, `updated_at`) VALUES
        (1, 'Request for pickup', 8, 'Experience the power of our on-demand trucking service platform, connecting people seamlessly.', 1, 1, '2023-01-27 17:30:39', '2023-04-28 23:48:54'),
        (2, 'Load goods and Moving', 9, 'Experience the power of our on-demand trucking service platform, connecting people seamlessly.', 1, 2, '2023-01-27 17:30:39', '2023-01-27 18:10:16'),
        (3, 'Unload goods and payment', 10, 'Experience the power of our on-demand trucking service platform, connecting people seamlessly.', 1, 3, '2023-01-27 17:30:39', '2023-04-28 23:48:44')");
    }
}
