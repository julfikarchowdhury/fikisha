<?php
  use App\Enums\ParcelStatus;
return [
  ParcelStatus::PICKUP_ASSIGN                          => 'تعيين الاستلام',
  ParcelStatus::PICKUP_RE_SCHEDULE                     => 'إعادة جدولة الاستلام',
  ParcelStatus::RECEIVED_BY_PICKUP_MAN                 => 'تم الاستلام من موظف الاستلام',
  ParcelStatus::RECEIVED_WAREHOUSE                     => 'تسليم إلى مركز الفرز',
  ParcelStatus::TRANSFER_TO_HUB                        => 'في طريقه إلى المركز',
  ParcelStatus::RECEIVED_BY_HUB                        => 'تم الاستلام في المركز',
  ParcelStatus::DELIVERY_MAN_ASSIGN                    => 'تعيين السائق',
  ParcelStatus::DELIVERY_RE_SCHEDULE                   => 'تنفيذ مجدول',
  ParcelStatus::RETURN_TO_COURIER                      => 'إرجاع إلى الساعي',
  ParcelStatus::PARTIAL_DELIVERED                      => 'تم التسليم جزئياً',
  ParcelStatus::DELIVERED                              => 'تم التسليم',
  ParcelStatus::RETURN_ASSIGN_TO_MERCHANT              => 'تعيين العودة للمرسل',
  ParcelStatus::RETURN_MERCHANT_RE_SCHEDULE            => 'إعادة جدولة تعيين العودة للمرسل',
  ParcelStatus::RETURN_RECEIVED_BY_MERCHANT            => 'تم استلام الإرجاع من المرسل',
  ParcelStatus::RETURN_WAREHOUSE                       => 'إرجاع إلى المخزن',
  ParcelStatus::ASSIGN_MERCHANT                        => 'تعيين للمرسل',
  ParcelStatus::RETURNED_MERCHANT                      => 'تم إرجاع المرسل',
  ParcelStatus::DROP_OFf_HUB1                          => 'تم التسليم في المركز',
  ParcelStatus::TRANSIT_OUT_CITY                       => 'عبور خارج المدينة',
  ParcelStatus::ON_THE_WAY_TO_CITY                     => 'في الطريق إلى المدينة',
  ParcelStatus::ARRIVED_AT_CITY                        => 'وصل إلى المدينة',
  ParcelStatus::DELIVERY_MAN_ASSIGN_SECOND_PROVINCE    => 'تم التعيين إلى المحافظة الثانية',
  ParcelStatus::DROP_OFF_CITY                          => 'تم التسليم في المدينة',

];
