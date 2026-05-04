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
        SmsSetting::create(['key' => 'reve_api_key',     'value' => 'a7e4166cc9967d80']);
        SmsSetting::create(['key' => 'reve_secret_key', 'value' => 'e863dd2f']);
        SmsSetting::create(['key' => 'reve_api_url',        'value' => 'http://smpp.ajuratech.com:7788/sendtext']);
        SmsSetting::create(['key' => 'reve_username',        'value' => '']);
        SmsSetting::create(['key' => 'reve_user_password',        'value' => '']);
        SmsSetting::create(['key' => 'reve_status',        'value' => Status::INACTIVE]);
        //Twilio SMS
        SmsSetting::create(['key' => 'twilio_sid',     'value' => 'ACf06720e95c070529e702d0fc90f5bab4']);
        SmsSetting::create(['key' => 'twilio_token', 'value' => 'bc1qre8jdw2azrg6tf49wmp652w00xltddxmpk98xp']);
        SmsSetting::create(['key' => 'twilio_from', 'value' => '+17123723883']);
        SmsSetting::create(['key' => 'twilio_status', 'value' => Status::INACTIVE]);

        //NEXMO SMS
        SmsSetting::create(['key' => 'nexmo_key',     'value' => '']);
        SmsSetting::create(['key' => 'nexmo_secret_key', 'value' => '']);
        SmsSetting::create(['key' => 'nexmo_status', 'value' => Status::INACTIVE]);

        //Easy send SMS
        SmsSetting::create(['key' => 'easysendsms_username',     'value' => 'ibraqhse7novy2023']);
        SmsSetting::create(['key' => 'easysendsms_password',     'value' => 'ess848']);
        SmsSetting::create(['key' => 'easysendsms_from',         'value' => 'Test']);
        SmsSetting::create(['key' => 'easysendsms_status',       'value' => Status::INACTIVE]);

        //bulk gate SMS
        SmsSetting::create(['key' => 'application_id',     'value' => '32309']);
        SmsSetting::create(['key' => 'application_token',     'value' => '8VazRws7CiNRylAFu76hBomWAXo7bNObj9IYQooDjL434ozo4j']);
        SmsSetting::create(['key' => 'bulk_gate_status',       'value' => Status::INACTIVE]);
    }
}
