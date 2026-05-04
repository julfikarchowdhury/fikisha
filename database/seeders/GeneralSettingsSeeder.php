<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Backend\GeneralSettings;
use App\Models\Backend\Upload;

class GeneralSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user               = new Upload();
        $user->original     = "uploads/users/user8.png";
        $user->save();

        $user               = new Upload();
        $user->original     = "uploads/users/user9.png";
        $user->save();

        $row                        = new GeneralSettings();
        $row->name                  = "We Delivery";
        $row->phone                 = "20022002";
        $row->email                 = "info@wemaxdevs.com";
        $row->address               = "Mirpur-2";
        $row->currency              = "$";
        $row->copyright             = "Copyright © All rights reserved. Development by WemaxDevs.";
        $row->about                 = "It is a long established fact that a reader will be distracted by the readable content.";
        $row->logo                  = null;
        $row->favicon               = null;
        $row->par_track_prefix      = 'dc';
        $row->invoice_prefix        = 'dc';
        $row->order_invoice_prefix  = 'WD2024';
        $row->current_version       = '1.2';
        $row->primary_color         = 'var(--bs-primary)';
        $row->text_color            = '#ffffff';
        $row->location_system       = 2;
        $row->inside_city_distance  = 60;
        $row->country_code  = '+1';
        $row->default_country  = 236;
        $row->save();
        return $row;
    }
}
