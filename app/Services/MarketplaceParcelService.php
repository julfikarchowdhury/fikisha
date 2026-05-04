<?php

namespace App\Services;

use App\Enums\ParcelStatus;
use App\Events\RiderLocationUpdated;
use App\Helpers\DeliveryChargeHelper;
use App\Models\Backend\DeliveryMan;
use App\Models\Backend\Parcel;
use App\Models\Backend\RiderLocation;
use App\Http\Services\SmsService;
use App\Services\RiderWalletService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class MarketplaceParcelService
{
    private const PICKUP_OTP_EXPIRES_MINUTES = 10;

    public function availableParcels(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Parcel::query()
            ->whereNull('delivery_man_id')
            ->whereIn('status', [
                ParcelStatus::MARKETPLACE_PENDING,
                ParcelStatus::MARKETPLACE_SENDER_PAYMENT_AWAITING,
                ParcelStatus::MARKETPLACE_RECEIVER_PAYMENT_AWAITING,
            ])
            ->orderByDesc('created_at');

        if (!empty($filters['from_state_id'])) {
            $query->where('from_state_id', $filters['from_state_id']);
        }
        if (!empty($filters['to_state_id'])) {
            $query->where('to_state_id', $filters['to_state_id']);
        }
        if (!empty($filters['from_city_id'])) {
            $query->where('from_city_id', $filters['from_city_id']);
        }
        if (!empty($filters['to_city_id'])) {
            $query->where('to_city_id', $filters['to_city_id']);
        }
        if (!empty($filters['min_distance_km'])) {
            $query->where('distance_km', '>=', $filters['min_distance_km']);
        }
        if (!empty($filters['max_distance_km'])) {
            $query->where('distance_km', '<=', $filters['max_distance_km']);
        }
        if (!empty($filters['min_cash_collection'])) {
            $query->where('cash_collection', '>=', $filters['min_cash_collection']);
        }
        if (!empty($filters['max_cash_collection'])) {
            $query->where('cash_collection', '<=', $filters['max_cash_collection']);
        }
        if (!empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }
        if (!empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }

        return $query->paginate($perPage);
    }

    public function activeParcels(int $riderId, int $perPage = 15): LengthAwarePaginator
    {
        return Parcel::query()
            ->where('delivery_man_id', $riderId)
            ->where('status', ParcelStatus::MARKETPLACE_ACCEPTED)
            ->orderByDesc('accepted_at')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function deliveredParcels(int $riderId, int $perPage = 15): LengthAwarePaginator
    {
        return Parcel::query()
            ->where('delivery_man_id', $riderId)
            ->where('status', ParcelStatus::MARKETPLACE_DELIVERED)
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function riderParcelsByStatus(int $riderId, string $statusKey, int $perPage = 15): LengthAwarePaginator
    {
        $statusMap = [
            'accepted' => ParcelStatus::MARKETPLACE_ACCEPTED,
            'picked_up' => ParcelStatus::MARKETPLACE_PICKED_UP,
            'delivered' => ParcelStatus::MARKETPLACE_DELIVERED,
            'canceled' => ParcelStatus::MARKETPLACE_CANCELLED,
        ];

        $query = Parcel::query()->where('delivery_man_id', $riderId);

        if ($statusKey === 'all') {
            $query->whereIn('status', array_values($statusMap));
        } else {
            $query->where('status', $statusMap[$statusKey]);
        }

        return $query
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function accept(Parcel $parcel, int $riderId): array
    {
        return DB::transaction(function () use ($parcel, $riderId) {
            $lockedParcel = Parcel::whereKey($parcel->id)->lockForUpdate()->first();

            if (!$lockedParcel) {
                return [
                    'status' => 404,
                    'payload' => ['success' => false, 'message' => 'Parcel not found.'],
                ];
            }

            if (!in_array((int) $lockedParcel->status, [
                ParcelStatus::MARKETPLACE_PENDING,
                ParcelStatus::MARKETPLACE_SENDER_PAYMENT_AWAITING,
                ParcelStatus::MARKETPLACE_RECEIVER_PAYMENT_AWAITING,
            ], true)) {
                return [
                    'status' => 409,
                    'payload' => ['success' => false, 'message' => 'Parcel already accepted.'],
                ];
            }

            $maxActiveParcels = (int) (settings()->max_active_parcels_per_rider ?? 1);
            if ($maxActiveParcels < 1) {
                $maxActiveParcels = 1;
            }

            $activeParcelCount = Parcel::where('delivery_man_id', $riderId)
                ->whereIn('status', [
                    ParcelStatus::MARKETPLACE_ACCEPTED,
                    ParcelStatus::MARKETPLACE_PICKED_UP,
                ])
                ->count();

            if ($activeParcelCount >= $maxActiveParcels) {
                return [
                    'status' => 422,
                    'payload' => ['success' => false, 'message' => 'Rider already has the maximum active parcels.'],
                ];
            }

            $trackingToken = (string) Str::uuid();

            $lockedParcel->delivery_man_id = $riderId;
            $lockedParcel->status = ParcelStatus::MARKETPLACE_ACCEPTED;
            $lockedParcel->accepted_at = now();
            $lockedParcel->tracking_token = $trackingToken;
            $lockedParcel->save();

            return [
                'status' => 200,
                'payload' => [
                    'success' => true,
                    'tracking_url' => url('/track/' . $trackingToken),
                ],
            ];
        });
    }

    public function updateStatus(Parcel $parcel, int $riderId, int $nextStatus): array
    {
        return DB::transaction(function () use ($parcel, $riderId, $nextStatus) {
            $lockedParcel = Parcel::whereKey($parcel->id)->lockForUpdate()->first();

            if (!$lockedParcel) {
                return [
                    'status' => 404,
                    'payload' => ['success' => false, 'message' => 'Parcel not found.'],
                ];
            }

            if ((int) $lockedParcel->delivery_man_id !== $riderId) {
                return [
                    'status' => 403,
                    'payload' => ['success' => false, 'message' => 'You do not own this parcel.'],
                ];
            }

            $allowedTransitions = [
                ParcelStatus::MARKETPLACE_ACCEPTED => ParcelStatus::MARKETPLACE_PICKED_UP,
                ParcelStatus::MARKETPLACE_PICKED_UP => ParcelStatus::MARKETPLACE_DELIVERED,
            ];

            $currentStatus = (int) $lockedParcel->status;
            if (!array_key_exists($currentStatus, $allowedTransitions)) {
                return [
                    'status' => 409,
                    'payload' => ['success' => false, 'message' => 'Status transition not allowed.'],
                ];
            }

            if ($allowedTransitions[$currentStatus] !== $nextStatus) {
                return [
                    'status' => 409,
                    'payload' => ['success' => false, 'message' => 'Status transition not allowed.'],
                ];
            }

            if ($nextStatus === ParcelStatus::MARKETPLACE_DELIVERED && empty($lockedParcel->receiver_otp_verified_at)) {
                return [
                    'status' => 409,
                    'payload' => [
                        'success' => false,
                        'message' => 'Receiver OTP verification is required before delivery.',
                    ],
                ];
            }

            if ($nextStatus === ParcelStatus::MARKETPLACE_PICKED_UP && !$this->isPickupOtpVerified($lockedParcel->id, $riderId)) {
                return [
                    'status' => 409,
                    'payload' => [
                        'success' => false,
                        'message' => 'Pickup OTP verification is required before pickup.',
                    ],
                ];
            }

            if ($nextStatus === ParcelStatus::MARKETPLACE_DELIVERED) {
                $whoPays = (int) ($lockedParcel->who_pays_either ?? 0);
                $paymentStatus = (int) ($lockedParcel->payment_status ?? 0);
                if ($paymentStatus !== \App\Enums\InvoiceStatus::PAID) {
                    return [
                        'status' => 409,
                        'payload' => [
                            'success' => false,
                            'message' => 'Payment is unpaid. Delivery cannot be completed.',
                        ],
                    ];
                }
            }

            $lockedParcel->status = $nextStatus;
            if ($nextStatus === ParcelStatus::MARKETPLACE_DELIVERED) {
                if ((float) ($lockedParcel->commission_amount ?? 0) > 0) {
                    return [
                        'status' => 409,
                        'payload' => [
                            'success' => false,
                            'message' => 'Delivery already finalized for this parcel.',
                        ],
                    ];
                }

                if ((float) ($lockedParcel->base_delivery_charge ?? 0) <= 0) {
                    $distanceKm = (float) ($lockedParcel->distance_km ?? 0);
                    $weightKg = (float) ($lockedParcel->total_weight ?? $lockedParcel->weight ?? 0);
                    $whoPays = (int) ($lockedParcel->who_pays_either ?? 0);
                    $breakdown = (new DeliveryChargeHelper())->marketplacePricingBreakdown($distanceKm, $weightKg, $whoPays);
                    $lockedParcel->base_delivery_charge = $breakdown['base'];
                    $lockedParcel->receiver_markup = $breakdown['markup'];
                    $lockedParcel->final_paid_amount = $breakdown['final'];
                    $lockedParcel->commission_percent = (float) (settings()->marketplace_commission_percent ?? 0);
                }

                $baseCharge = (float) ($lockedParcel->base_delivery_charge ?? $lockedParcel->delivery_charge ?? 0);
                $commissionPercent = (float) ($lockedParcel->commission_percent ?? settings()->marketplace_commission_percent ?? 0);
                $commissionAmount = round(($baseCharge * $commissionPercent) / 100, 2);
                $lockedParcel->commission_amount = $commissionAmount;
                $lockedParcel->rider_earning = round($baseCharge - $commissionAmount, 2);
                $receiverMarkup = (float) ($lockedParcel->receiver_markup ?? 0);
                $lockedParcel->platform_total_earning = round($commissionAmount + $receiverMarkup, 2);
            }
            $lockedParcel->save();

            if ($nextStatus === ParcelStatus::MARKETPLACE_PICKED_UP) {
                $this->clearPickupOtpState($lockedParcel->id, $riderId);
            }

            if ($nextStatus === ParcelStatus::MARKETPLACE_DELIVERED) {
                app(RiderWalletService::class)->creditForParcel($lockedParcel);
            }

            return [
                'status' => 200,
                'payload' => [
                    'success' => true,
                    'message' => 'Parcel status updated successfully.',
                ],
            ];
        });
    }

    public function cancel(Parcel $parcel, int $riderId): array
    {
        return DB::transaction(function () use ($parcel, $riderId) {
            $lockedParcel = Parcel::whereKey($parcel->id)->lockForUpdate()->first();

            if (!$lockedParcel) {
                return [
                    'status' => 404,
                    'payload' => ['success' => false, 'message' => 'Parcel not found.'],
                ];
            }

            if ((int) $lockedParcel->delivery_man_id !== $riderId) {
                return [
                    'status' => 403,
                    'payload' => ['success' => false, 'message' => 'You do not own this parcel.'],
                ];
            }

            if ((int) $lockedParcel->status !== ParcelStatus::MARKETPLACE_ACCEPTED) {
                return [
                    'status' => 409,
                    'payload' => [
                        'success' => false,
                        'message' => 'Parcel can only be cancelled before pickup.',
                    ],
                ];
            }

            $lockedParcel->delivery_man_id = null;
            $lockedParcel->accepted_at = null;
            $lockedParcel->tracking_token = null;
            $lockedParcel->status = ParcelStatus::MARKETPLACE_PENDING;
            $lockedParcel->save();

            $this->clearPickupOtpState($lockedParcel->id, $riderId);

            return [
                'status' => 200,
                'payload' => [
                    'success' => true,
                    'message' => 'Parcel cancelled successfully.',
                ],
            ];
        });
    }

    public function sendPickupOtp(Parcel $parcel, int $riderId): array
    {
        return DB::transaction(function () use ($parcel, $riderId) {
            $lockedParcel = Parcel::whereKey($parcel->id)->lockForUpdate()->first();

            if (!$lockedParcel) {
                return [
                    'status' => 404,
                    'payload' => ['success' => false, 'message' => 'Parcel not found.'],
                ];
            }

            if ((int) $lockedParcel->delivery_man_id !== $riderId) {
                return [
                    'status' => 403,
                    'payload' => ['success' => false, 'message' => 'You do not own this parcel.'],
                ];
            }

            if ((int) $lockedParcel->status !== ParcelStatus::MARKETPLACE_ACCEPTED) {
                return [
                    'status' => 409,
                    'payload' => ['success' => false, 'message' => 'Pickup OTP can only be sent after accept and before pickup.'],
                ];
            }

            $phone = trim((string) ($lockedParcel->sender_phone
                ?? $lockedParcel->pickup_phone
                ?? optional(optional($lockedParcel->merchant)->user)->mobile
                ?? ''));
            if ($phone === '') {
                return [
                    'status' => 422,
                    'payload' => ['success' => false, 'message' => 'Sender phone number is missing.'],
                ];
            }

            $otp = (string) random_int(100000, 999999);
            Cache::put(
                $this->pickupOtpCacheKey($lockedParcel->id, $riderId),
                $otp,
                now()->addMinutes(self::PICKUP_OTP_EXPIRES_MINUTES)
            );
            Cache::forget($this->pickupOtpVerifiedCacheKey($lockedParcel->id, $riderId));

            $smsResult = app(SmsService::class)->sendOtp($phone, $otp);
            if (empty($smsResult['success'])) {
                return [
                    'status' => 422,
                    'payload' => [
                        'success' => false,
                        'message' => $smsResult['message'] ?? 'OTP failed to send.',
                        'gateway' => $smsResult['gateway'] ?? null,
                    ],
                ];
            }

            return [
                'status' => 200,
                'payload' => [
                    'success' => true,
                    'message' => 'Pickup OTP sent to sender.',
                    'gateway' => $smsResult['gateway'] ?? null,
                    'expires_in_seconds' => self::PICKUP_OTP_EXPIRES_MINUTES * 60,
                ],
            ];
        });
    }

    public function verifyPickupOtp(Parcel $parcel, int $riderId, string $otp): array
    {
        return DB::transaction(function () use ($parcel, $riderId, $otp) {
            $lockedParcel = Parcel::whereKey($parcel->id)->lockForUpdate()->first();

            if (!$lockedParcel) {
                return [
                    'status' => 404,
                    'payload' => ['success' => false, 'message' => 'Parcel not found.'],
                ];
            }

            if ((int) $lockedParcel->delivery_man_id !== $riderId) {
                return [
                    'status' => 403,
                    'payload' => ['success' => false, 'message' => 'You do not own this parcel.'],
                ];
            }

            if ((int) $lockedParcel->status !== ParcelStatus::MARKETPLACE_ACCEPTED) {
                return [
                    'status' => 409,
                    'payload' => ['success' => false, 'message' => 'Pickup OTP verification is only allowed before pickup.'],
                ];
            }

            $cachedOtp = (string) Cache::get($this->pickupOtpCacheKey($lockedParcel->id, $riderId), '');
            if ($cachedOtp === '') {
                return [
                    'status' => 409,
                    'payload' => ['success' => false, 'message' => 'Pickup OTP has not been sent or expired.'],
                ];
            }

            if (trim($cachedOtp) !== trim((string) $otp)) {
                return [
                    'status' => 422,
                    'payload' => ['success' => false, 'message' => 'Invalid OTP.'],
                ];
            }

            Cache::put(
                $this->pickupOtpVerifiedCacheKey($lockedParcel->id, $riderId),
                true,
                now()->addMinutes(self::PICKUP_OTP_EXPIRES_MINUTES)
            );
            Cache::forget($this->pickupOtpCacheKey($lockedParcel->id, $riderId));

            return [
                'status' => 200,
                'payload' => [
                    'success' => true,
                    'message' => 'Pickup OTP verified successfully.',
                ],
            ];
        });
    }

    public function sendReceiverOtp(Parcel $parcel, int $riderId): array
    {
        return DB::transaction(function () use ($parcel, $riderId) {
            $lockedParcel = Parcel::whereKey($parcel->id)->lockForUpdate()->first();

            if (!$lockedParcel) {
                return [
                    'status' => 404,
                    'payload' => ['success' => false, 'message' => 'Parcel not found.'],
                ];
            }

            if ((int) $lockedParcel->delivery_man_id !== $riderId) {
                return [
                    'status' => 403,
                    'payload' => ['success' => false, 'message' => 'You do not own this parcel.'],
                ];
            }

            if ((int) $lockedParcel->status !== ParcelStatus::MARKETPLACE_PICKED_UP) {
                return [
                    'status' => 409,
                    'payload' => ['success' => false, 'message' => 'OTP can only be sent after pickup.'],
                ];
            }

            if (empty($lockedParcel->customer_phone)) {
                return [
                    'status' => 422,
                    'payload' => ['success' => false, 'message' => 'Receiver phone number is missing.'],
                ];
            }

            $otp = (string) random_int(100000, 999999);
            $lockedParcel->receiver_otp = $otp;
            $lockedParcel->receiver_otp_sent_at = now();
            $lockedParcel->receiver_otp_verified_at = null;
            $lockedParcel->save();

            $smsResult = app(SmsService::class)->sendOtp($lockedParcel->customer_phone, $otp);
            if (empty($smsResult['success'])) {
                return [
                    'status' => 422,
                    'payload' => [
                        'success' => false,
                        'message' => $smsResult['message'] ?? 'OTP failed to send.',
                        'gateway' => $smsResult['gateway'] ?? null,
                    ],
                ];
            }

            return [
                'status' => 200,
                'payload' => [
                    'success' => true,
                    'message' => 'OTP sent to receiver.',
                    'gateway' => $smsResult['gateway'] ?? null,
                ],
            ];
        });
    }

    public function verifyReceiverOtp(Parcel $parcel, int $riderId, string $otp): array
    {
        return DB::transaction(function () use ($parcel, $riderId, $otp) {
            $lockedParcel = Parcel::whereKey($parcel->id)->lockForUpdate()->first();

            if (!$lockedParcel) {
                return [
                    'status' => 404,
                    'payload' => ['success' => false, 'message' => 'Parcel not found.'],
                ];
            }

            if ((int) $lockedParcel->delivery_man_id !== $riderId) {
                return [
                    'status' => 403,
                    'payload' => ['success' => false, 'message' => 'You do not own this parcel.'],
                ];
            }

            if ((int) $lockedParcel->status !== ParcelStatus::MARKETPLACE_PICKED_UP) {
                return [
                    'status' => 409,
                    'payload' => ['success' => false, 'message' => 'OTP verification is only allowed after pickup.'],
                ];
            }

            if (empty($lockedParcel->receiver_otp)) {
                return [
                    'status' => 409,
                    'payload' => ['success' => false, 'message' => 'OTP has not been sent yet.'],
                ];
            }

            if (trim((string) $lockedParcel->receiver_otp) !== trim((string) $otp)) {
                return [
                    'status' => 422,
                    'payload' => ['success' => false, 'message' => 'Invalid OTP.'],
                ];
            }

            $lockedParcel->receiver_otp_verified_at = now();
            $lockedParcel->receiver_otp = null;
            $lockedParcel->save();

            return [
                'status' => 200,
                'payload' => [
                    'success' => true,
                    'message' => 'Receiver verified successfully.',
                ],
            ];
        });
    }

    public function updateLocation(int $parcelId, int $riderId, float $lat, float $lng): array
    {
        return DB::transaction(function () use ($parcelId, $riderId, $lat, $lng) {
            $parcel = Parcel::whereKey($parcelId)->lockForUpdate()->first();

            if (!$parcel) {
                return [
                    'status' => 404,
                    'payload' => ['success' => false, 'message' => 'Parcel not found.'],
                ];
            }

            if ((int) $parcel->delivery_man_id !== $riderId) {
                return [
                    'status' => 403,
                    'payload' => ['success' => false, 'message' => 'You do not own this parcel.'],
                ];
            }

            if (!in_array((int) $parcel->status, [
                ParcelStatus::MARKETPLACE_ACCEPTED,
                ParcelStatus::MARKETPLACE_PICKED_UP,
            ], true)) {
                return [
                    'status' => 409,
                    'payload' => ['success' => false, 'message' => 'Location update not allowed for this status.'],
                ];
            }

            if (empty($parcel->tracking_token)) {
                return [
                    'status' => 409,
                    'payload' => ['success' => false, 'message' => 'Tracking token not found for this parcel.'],
                ];
            }

            $previousLocation = RiderLocation::where('parcel_id', $parcel->id)->first();
            $location = RiderLocation::updateOrCreate(
                ['parcel_id' => $parcel->id],
                [
                    'rider_id' => $riderId,
                    'lat' => $lat,
                    'lng' => $lng,
                ]
            );

            $secondsSincePrevious = null;
            if ($previousLocation && $previousLocation->updated_at) {
                $secondsSincePrevious = $location->updated_at->diffInSeconds($previousLocation->updated_at);
            }

            Log::info('rider.location.updated', [
                'rider_id' => $riderId,
                'parcel_id' => (int) $parcel->id,
                'tracking_token' => (string) $parcel->tracking_token,
                'status' => (int) $parcel->status,
                'lat' => (float) $lat,
                'lng' => (float) $lng,
                'seconds_since_previous_update' => $secondsSincePrevious,
                'updated_at' => $location->updated_at->toDateTimeString(),
            ]);

            broadcast(new RiderLocationUpdated(
                (string) $parcel->tracking_token,
                $lat,
                $lng,
                (int) $parcel->status,
                $location->updated_at->toDateTimeString()
            ));

            return [
                'status' => 200,
                'payload' => [
                    'success' => true,
                    'message' => 'Location updated successfully.',
                ],
            ];
        });
    }

    public function trackingData(string $trackingToken): array
    {
        $parcel = Parcel::where('tracking_token', $trackingToken)->first();

        if (!$parcel) {
            return [
                'status' => 404,
                'payload' => ['success' => false, 'message' => 'Tracking token not found.'],
            ];
        }

        $location = RiderLocation::where('parcel_id', $parcel->id)->first();

        // Fallback 1: if parcel-scoped rider location is missing, use rider's latest location.
        if (!$location && !empty($parcel->delivery_man_id)) {
            $location = RiderLocation::where('rider_id', (int) $parcel->delivery_man_id)
                ->latest('updated_at')
                ->first();
        }

        $fallbackLat = null;
        $fallbackLng = null;

        // Fallback 2: use delivery man's last known coordinates from profile.
        if (!$location && !empty($parcel->delivery_man_id)) {
            $deliveryMan = DeliveryMan::where('user_id', (int) $parcel->delivery_man_id)->first();
            if ($deliveryMan && $deliveryMan->delivery_lat && $deliveryMan->delivery_long) {
                $fallbackLat = (float) $deliveryMan->delivery_lat;
                $fallbackLng = (float) $deliveryMan->delivery_long;
            }
        }

        return [
            'status' => 200,
            'payload' => [
                'success' => true,
                'data' => [
                    'status' => (int) $parcel->status,
                    'status_name' => trans('parcelStatus.' . $parcel->status),
                    'pickup_address' => $parcel->pickup_address,
                    'drop_address' => $parcel->customer_address,
                    'pickup_lat' => $parcel->pickup_lat ? (float) $parcel->pickup_lat : null,
                    'pickup_lng' => $parcel->pickup_long ? (float) $parcel->pickup_long : null,
                    'drop_lat' => $parcel->drop_latitude ? (float) $parcel->drop_latitude : null,
                    'drop_lng' => $parcel->drop_longitude ? (float) $parcel->drop_longitude : null,
                    'rider_lat' => $location ? (float) $location->lat : $fallbackLat,
                    'rider_lng' => $location ? (float) $location->lng : $fallbackLng,
                    'updated_at' => $location ? $location->updated_at->toDateTimeString() : null,
                ],
            ],
        ];
    }

    private function pickupOtpCacheKey(int $parcelId, int $riderId): string
    {
        return 'pickup_otp_' . $parcelId . '_' . $riderId;
    }

    private function pickupOtpVerifiedCacheKey(int $parcelId, int $riderId): string
    {
        return 'pickup_otp_verified_' . $parcelId . '_' . $riderId;
    }

    private function isPickupOtpVerified(int $parcelId, int $riderId): bool
    {
        return (bool) Cache::get($this->pickupOtpVerifiedCacheKey($parcelId, $riderId), false);
    }

    private function clearPickupOtpState(int $parcelId, int $riderId): void
    {
        Cache::forget($this->pickupOtpCacheKey($parcelId, $riderId));
        Cache::forget($this->pickupOtpVerifiedCacheKey($parcelId, $riderId));
    }
}

