<?php

namespace App\Enums;

interface InvoiceStatus
{
    const UNPAID        = 1;
    const PROCESSING    = 2;
    const PAID          = 3;
}
