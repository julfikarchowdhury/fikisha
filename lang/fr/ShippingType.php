<?php

use App\Enums\ShippingType;

return [
    ShippingType::DOOR_TO_DOOR => 'Porte à porte',
    ShippingType::DOOR_TO_HUB  => 'Porte à agence',
    ShippingType::HUB_TO_HUB   => 'Agence à agence',
    ShippingType::HUB_TO_DOOR  => 'Agence à porte',
];