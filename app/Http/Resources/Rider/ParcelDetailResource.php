<?php

namespace App\Http\Resources\Rider;

use Illuminate\Http\Resources\Json\JsonResource;

class ParcelDetailResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'tracking_id' => $this->tracking_id,
            'status' => (int) $this->status,
            'status_name' => trans('parcelStatus.' . $this->status),
            'receiver_otp_verified' => (bool) $this->receiver_otp_verified_at,
            'otp_required_for_delivery' => ((int) $this->status === \App\Enums\ParcelStatus::MARKETPLACE_PICKED_UP)
                && empty($this->receiver_otp_verified_at),
            'pickup_address' => (string) $this->pickup_address,
            'pickup_lat' => $this->pickup_lat ? (float) $this->pickup_lat : null,
            'pickup_lng' => $this->pickup_long ? (float) $this->pickup_long : null,
            'drop_address' => (string) $this->customer_address,
            'drop_lat' => $this->drop_latitude ? (float) $this->drop_latitude : null,
            'drop_lng' => $this->drop_longitude ? (float) $this->drop_longitude : null,
            'pickup_phone' => (string) $this->pickup_phone,
            'sender_name' => trim(($this->sender_first_name ?? '') . ' ' . ($this->sender_last_name ?? '')),
            'receiver_name' => trim(($this->customer_first_name ?? '') . ' ' . ($this->customer_last_name ?? '')),
            'receiver_phone' => (string) $this->customer_phone,
            'cash_collection' => (string) $this->cash_collection,
            'delivery_charge' => (string) $this->delivery_charge,
            'total_delivery_amount' => (string) $this->total_delivery_amount,
            'distance_km' => (string) $this->distance_km,
            'weight' => (string) $this->weight,
            'note' => (string) $this->note,
            'rider_coordinates_time' => 10,
            'accepted_at' => $this->accepted_at instanceof \DateTimeInterface
                ? $this->accepted_at->toDateTimeString()
                : ($this->accepted_at ?: null),
            'created_at' => $this->created_at instanceof \DateTimeInterface
                ? $this->created_at->toDateTimeString()
                : ($this->created_at ?: null),
        ];
    }
}

