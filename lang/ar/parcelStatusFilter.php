<?php

use App\Enums\ParcelStatus;

return array(
    ParcelStatus::PENDING                                => 'غير معين',
    ParcelStatus::DELIVERY_MAN_ASSIGN                    => 'مُكَلَّف',
    ParcelStatus::PROCESSING                             => 'يعالج',
    ParcelStatus::DELIVERED                              => 'تم التوصيل',
    ParcelStatus::DELIVERY_FAILURE                       => 'فشل',
);
