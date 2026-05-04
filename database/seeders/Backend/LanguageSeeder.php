<?php

namespace Database\Seeders\Backend;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::statement("INSERT INTO `languages` (`id`, `name`, `code`, `icon_class`, `text_direction`, `status`, `created_at`, `updated_at`) VALUES
        (1, 'English', 'en', 'flag-icon flag-icon-us', 'LTR', 1, '2024-10-16 04:40:21', '2024-10-16 04:40:21'),
        (3, 'Arabic', 'ar', 'flag-icon flag-icon-sa', 'RTL', 1, '2024-10-16 04:40:21', '2024-10-16 04:40:21'),
        (6, 'French', 'fr', 'flag-icon flag-icon-fr', 'LTR', 1, '2024-10-16 04:40:21', '2024-10-16 04:40:21');
        ");
    }
}
