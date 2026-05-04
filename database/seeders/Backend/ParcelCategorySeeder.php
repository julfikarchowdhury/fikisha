<?php

namespace Database\Seeders\Backend;

use App\Models\Backend\ParcelCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
class ParcelCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parcel_categories = [
            'Home & Kitchen',
            'Beauty & Personal Car',
            'Clothing, Shoes & Jewelry',
            'Toys & games',
            'Health, Household & Baby Care',
            'Baby',
            'Electronics',
            'Sports & outdoors',
            'Pet Supplies'
        ];
        foreach ($parcel_categories as $key => $name) {
            ParcelCategory::create([
                'name'         => $name,
                'position'     => (int)(1+$key)
            ]);
        }
    }
}
