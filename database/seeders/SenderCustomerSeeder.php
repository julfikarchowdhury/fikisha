<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Enums\UserType;
use App\Models\Backend\SenderCustomer;

class SenderCustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user                   = new SenderCustomer();
        $user->sender_id        = 1;
        $user->province_id      = 2;
        $user->city_id          = 2;
        $user->portal_code      = 1216;
        $user->first_name       = "SB";
        $user->last_name        = "Sajib";
        $user->phone_number     = "01912938002";
        $user->email            = "sajib@wemaxdevs.com";
        $user->whatsapp_number  = "01912938002";
        $user->address          = "Mirpur-10, Dhaka-1216";
        $user->save();

        $user                   = new SenderCustomer();
        $user->sender_id        = 1;
        $user->province_id      = 1;
        $user->city_id          = 1;
        $user->portal_code      = 1217;
        $user->first_name       = "Raj";
        $user->last_name        = "Khan";
        $user->phone_number     = "01912938001";
        $user->email            = "raj@wemaxdevs.com";
        $user->whatsapp_number  = "01912938002";
        $user->address          = "Mirpur-12, Dhaka-1217";
        $user->save();
    }
}
