<?php

namespace App\Enums;

interface RiderStatus
{
    public const PENDING_PHONE = 1;
    public const PENDING_KYC = 2;
    public const UNDER_REVIEW = 3;
    public const APPROVED = 4;
    public const REJECTED = 5;
    public const SUSPENDED = 6;
    public const BLOCKED = 7;

    public const LABELS = [
        self::PENDING_PHONE => 'Pending Phone',
        self::PENDING_KYC => 'Pending KYC',
        self::UNDER_REVIEW => 'Under Review',
        self::APPROVED => 'Approved',
        self::REJECTED => 'Rejected',
        self::SUSPENDED => 'Suspended',
        self::BLOCKED => 'Blocked',
    ];
}
