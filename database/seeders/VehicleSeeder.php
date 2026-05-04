<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\Backend\Vehicle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Vehicle::truncate();
        $data = [
            [
                'name'        => 'Track',
                'description' => 'Track one or multiple parcels with UPS Tracking.',
                'registration_number' => 4242424242424,
                'capacity'    => '500kg',
                'size'        => '500',
                'status'      => Status::ACTIVE,
            ]
        ];
        foreach ($data as $value) {
            Vehicle::create($value);
        }
    }
}
