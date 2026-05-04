<?php

use App\Enums\ParcelStatus;

return array (
    ParcelStatus::PENDING                                => 'Unassigned',
    ParcelStatus::DELIVERY_MAN_ASSIGN                    => 'Assigned',
    ParcelStatus::PROCESSING                             => 'Processing',
    ParcelStatus::DELIVERED                              => 'Delivered',
    ParcelStatus::DELIVERY_FAILURE                       => 'Failure',
);
