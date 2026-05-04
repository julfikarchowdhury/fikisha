<?php
namespace App\Enums;

interface ShippingType{
    const DOOR_TO_DOOR  = 1;
    const DOOR_TO_HUB   = 2;
    const HUB_TO_HUB    = 3;
    const HUB_TO_DOOR   = 4;
}