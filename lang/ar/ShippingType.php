<?php

use App\Enums\ShippingType;

return [
    ShippingType::DOOR_TO_DOOR => 'من الباب إلى الباب',
    ShippingType::DOOR_TO_HUB  => 'من الباب إلى المركز',
    ShippingType::HUB_TO_HUB   => 'من مركز إلى مركز',
    ShippingType::HUB_TO_DOOR  => 'من المركز إلى الباب',
];