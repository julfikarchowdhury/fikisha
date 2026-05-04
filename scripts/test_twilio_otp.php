<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "country_code=" . (settings()->country_code ?? '') . PHP_EOL;
echo "twilio_status=" . (smsSettings('twilio_status') ?? '') . PHP_EOL;
echo "twilio_from=" . (smsSettings('twilio_from') ?? '') . PHP_EOL;

$service = app(\App\Http\Services\SmsService::class);
$method = new ReflectionMethod($service, 'twilioSms');
$method->setAccessible(true);
$result = $method->invoke($service, 'otp', '+18777804236', '123456');

if ($result === true) {
    echo "twilio_result=OK" . PHP_EOL;
} else {
    echo "twilio_result=FAIL" . PHP_EOL;
}

echo "OK\n";
