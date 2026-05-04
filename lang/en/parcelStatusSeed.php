<?php
  use App\Enums\ParcelStatus;
return [
    ParcelStatus::PICKUP_ASSIGN                          => 'Pickup Assign',
    ParcelStatus::PICKUP_RE_SCHEDULE                     => 'Pickup Re-Schedule',
    ParcelStatus::RECEIVED_BY_PICKUP_MAN                 => 'Received By Pickup Man',
    ParcelStatus::RECEIVED_WAREHOUSE                     => 'Drop off hub',
    ParcelStatus::TRANSFER_TO_HUB                        => 'Heading to hub',
    ParcelStatus::RECEIVED_BY_HUB                        => 'Received by hub',
    ParcelStatus::DELIVERY_MAN_ASSIGN                    => 'Driver Assign',
    ParcelStatus::DELIVERY_RE_SCHEDULE                   => 'Scheduled execution',
    ParcelStatus::RETURN_TO_COURIER                      => 'Return to Courier',
    ParcelStatus::PARTIAL_DELIVERED                      => 'Partial Delivered',
    ParcelStatus::DELIVERED                              => 'Delivered',
    ParcelStatus::RETURN_ASSIGN_TO_MERCHANT              => 'Return assign to sender',
    ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE            => 'Return assign to sender Re-Schedule ',
    ParcelStatus::RETURN_RECEIVED_BY_MERCHANT            => 'Return received by sender',
    ParcelStatus::RETURN_WAREHOUSE                       => 'Return Warehouse',
    ParcelStatus::ASSIGN_MERCHANT                        => 'Assign sender',
    ParcelStatus::RETURNED_MERCHANT                      => 'Returned sender',
    ParcelStatus::DROP_OFf_HUB1                          => 'Dropped off at hub',
    ParcelStatus::TRANSIT_OUT_CITY                       => 'Transit out city',
    ParcelStatus::ON_THE_WAY_TO_CITY                     => 'On the way to city',
    ParcelStatus::ARRIVED_AT_CITY                        => 'Arrived at city',
    ParcelStatus::DELIVERY_MAN_ASSIGN_SECOND_PROVINCE    => 'Assigned Second Province',
    ParcelStatus::DROP_OFF_CITY                          => 'Dropped off city'
];