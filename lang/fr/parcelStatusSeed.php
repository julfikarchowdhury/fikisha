<?php

use App\Enums\ParcelStatus;

return array(
    ParcelStatus::PICKUP_ASSIGN                          => 'Assignation du ramassage',
    ParcelStatus::PICKUP_RE_SCHEDULE                     => 'Replanification du ramassage',
    ParcelStatus::RECEIVED_BY_PICKUP_MAN                 => 'Reçu par le ramasseur',
    ParcelStatus::RECEIVED_WAREHOUSE                     => 'Dépôt au hub',
    ParcelStatus::TRANSFER_TO_HUB                        => 'En route vers le hub',
    ParcelStatus::RECEIVED_BY_HUB                        => 'Reçu au hub',
    ParcelStatus::DELIVERY_MAN_ASSIGN                    => 'Assignation du chauffeur',
    ParcelStatus::DELIVERY_RE_SCHEDULE                   => 'Exécution programmée',
    ParcelStatus::RETURN_TO_COURIER                      => 'Retour au coursier',
    ParcelStatus::PARTIAL_DELIVERED                      => 'Livré partiellement',
    ParcelStatus::DELIVERED                              => 'Livré',
    ParcelStatus::RETURN_ASSIGN_TO_MERCHANT              => 'Retour assigné à l\'expéditeur',
    ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE            => 'Replanification du retour assigné à l\'expéditeur',
    ParcelStatus::RETURN_RECEIVED_BY_MERCHANT            => 'Retour reçu par l\'expéditeur',
    ParcelStatus::RETURN_WAREHOUSE                       => 'Retour à l\'entrepôt',
    ParcelStatus::ASSIGN_MERCHANT                        => 'Assigné à l\'expéditeur',
    ParcelStatus::RETURNED_MERCHANT                      => 'Expéditeur retourné',
    ParcelStatus::DROP_OFf_HUB1                          => 'Déposé au hub',
    ParcelStatus::TRANSIT_OUT_CITY                       => 'Transit hors de la ville',
    ParcelStatus::ON_THE_WAY_TO_CITY                     => 'En route vers la ville',
    ParcelStatus::ARRIVED_AT_CITY                        => 'Arrivé en ville',
    ParcelStatus::DELIVERY_MAN_ASSIGN_SECOND_PROVINCE    => 'Assigné à la deuxième province',
    ParcelStatus::DROP_OFF_CITY                          => 'Déposé en ville',
);
