<?php

namespace Database\Seeders;

use App\Enums\Status;
use App\Models\Backend\SmsSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SmsSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //SMS settings
        //REVE SMS
        SmsSetting::create(['key' => 'reve_api_key',     'value' => '']);
        SmsSetting::create(['key' => 'reve_secret_key', 'value' => '']);
        SmsSetting::create(['key' => 'reve_api_url',        'value' => '']);
        SmsSetting::create(['key' => 'reve_username',        'value' => '']);
        SmsSetting::create(['key' => 'reve_user_password',        'value' => '']);
        SmsSetting::create(['key' => 'reve_status',        'value' => Status::INACTIVE]);
        //Twilio SMS
        SmsSetting::create(['key' => 'twilio_sid',     'value' => '']);
        SmsSetting::create(['key' => 'twilio_token', 'value' => '']);
        SmsSetting::create(['key' => 'twilio_from', 'value' => '']);
        SmsSetting::create(['key' => 'twilio_status', 'value' => Status::INACTIVE]);

        //NEXMO SMS
        SmsSetting::create(['key' => 'nexmo_key',     'value' => '']);
        SmsSetting::create(['key' => 'nexmo_secret_key', 'value' => '']);
        SmsSetting::create(['key' => 'nexmo_status', 'value' => Status::INACTIVE]);

        //Easy send SMS
        SmsSetting::create(['key' => 'easysendsms_username',     'value' => '']);
        SmsSetting::create(['key' => 'easysendsms_password',     'value' => '']);
        SmsSetting::create(['key' => 'easysendsms_from',         'value' => '']);
        SmsSetting::create(['key' => 'easysendsms_status',       'value' => Status::INACTIVE]);

        //bulk gate SMS
        SmsSetting::create(['key' => 'application_id',     'value' => '']);
        SmsSetting::create(['key' => 'application_token',     'value' => '']);
        SmsSetting::create(['key' => 'bulk_gate_status',       'value' => Status::INACTIVE]);
    }
}
