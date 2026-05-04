<?php

namespace App\Http\Resources\Rider;

use Illuminate\Http\Resources\Json\JsonResource;
use DateTimeInterface;

class ActiveParcelResource extends JsonResource
{
    public function toArray($request)
    {
        $otpRequired = ((int) $this->status === \App\Enums\ParcelStatus::MARKETPLACE_PICKED_UP)
            && empty($this->receiver_otp_verified_at);

        return [
            'id' => $this->id,
            'pickup_address' => $this->pickup_address,
            'drop_address' => $this->customer_address,
            'status' => (int) $this->status,
            'otp_required_for_delivery' => $otpRequired,
            'weight' => (string) $this->weight,
            'delivery_charge' => (string) $this->delivery_charge,
            'accepted_at' => $this->accepted_at instanceof DateTimeInterface
                ? $this->accepted_at->toDateTimeString()
                : null,
        ];
    }
}

