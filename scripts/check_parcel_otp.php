<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$parcel = \App\Models\Backend\Parcel::find(2);
if (!$parcel) {
    echo "parcel_not_found\n";
    exit(1);
}

echo "parcel_id={$parcel->id}\n";
echo "status={$parcel->status}\n";
echo "delivery_man_id={$parcel->delivery_man_id}\n";
echo "customer_phone={$parcel->customer_phone}\n";

$service = app(\App\Http\Services\SmsService::class);
$result = $service->sendOtp($parcel->customer_phone, '123456');
echo "send_result=" . json_encode($result) . "\n";
