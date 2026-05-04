<?php
namespace App\Enums;

interface SmsSetup {
    const REVE          = 1;
    CONST TWILIO        = 2;
    CONST NEXMO         = 3;
    const EASYSENDSMS   = 4;
    const BULK_GATE     = 5;
}
