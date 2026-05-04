<?php

namespace Database\Seeders;

use App\Models\Backend\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        //social login settings (fill in Admin → Settings or use .env / config/services.php)
        Setting::create(['key' => 'facebook_client_id',     'value' => '']);
        Setting::create(['key' => 'facebook_client_secret', 'value' => '']);
        Setting::create(['key' => 'facebook_status',        'value' => 0]);
        Setting::create(['key' => 'google_client_id',     'value' => '']);
        Setting::create(['key' => 'google_client_secret', 'value' => '']);
        Setting::create(['key' => 'google_status',        'value' => 0]);

        //===== payment setup ===
        Setting::create(['key' => 'stripe_publishable_key',     'value' => '']);
        Setting::create(['key' => 'stripe_secret_key',          'value' => '']);
        Setting::create(['key' => 'stripe_status',              'value' => 0]);

        //Razorpay
        Setting::create(['key' => 'razorpay_key',               'value' => '']);
        Setting::create(['key' => 'razorpay_secret',            'value' => '']);
        Setting::create(['key' => 'razorpay_status',            'value' => 0]);

        //sslcommerz
        Setting::create(['key' => 'sslcommerz_store_id',        'value' => '']);
        Setting::create(['key' => 'sslcommerz_store_password',  'value' => '']);
        Setting::create(['key' => 'sslcommerz_testmode',        'value' => 1]);
        Setting::create(['key' => 'sslcommerz_status',          'value' => 0]);

        //paypal
        Setting::create(['key' => 'paypal_client_id',              'value' => '']);
        Setting::create(['key' => 'paypal_client_secret',          'value' => '']);
        Setting::create(['key' => 'paypal_mode',                   'value' => 'sandbox']);
        Setting::create(['key' => 'paypal_status',                 'value' => 0]);

        //skrill
        Setting::create(['key' => 'skrill_merchant_email',         'value' => '']);
        Setting::create(['key' => 'skrill_status',                 'value' => 0]);


        // //bkash
        Setting::create(['key' => 'bkash_app_id',              'value' => '']);
        Setting::create(['key' => 'bkash_app_secret',          'value' => '']);
        Setting::create(['key' => 'bkash_username',            'value' => '']);
        Setting::create(['key' => 'bkash_password',            'value' => '']);
        Setting::create(['key' => 'bkash_test_mode',           'value' => 1]);
        Setting::create(['key' => 'bkash_status',              'value' => 0]);


        //aamar pay
        Setting::create(['key' => 'aamarpay_store_id',        'value' => '']);
        Setting::create(['key' => 'aamarpay_signature_key',   'value' => '']);
        Setting::create(['key' => 'aamarpay_sendbox_mode',    'value' => 1]);
        Setting::create(['key' => 'aamarpay_status',          'value' => 0]);

        //=====payment setup===

    }
}
