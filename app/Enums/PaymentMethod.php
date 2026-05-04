<?php

namespace App\Enums;

interface PaymentMethod
{
    const CIB               = 1;
    const BANK_TRANSFER     = 2;
    const CHEQUE            = 3;
    const CASH              = 4;
}
