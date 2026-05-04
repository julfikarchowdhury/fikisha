<?php

namespace App\Http\Resources\Rider;

use Illuminate\Http\Resources\Json\JsonResource;

class MarketplaceParcelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'tracking_id' => $this->tracking_id,
            'status' => (int) $this->status,
            'pickup_address' => (string) $this->pickup_address,
            'pickup_phone' => (string) $this->pickup_phone,
            'sender_name' => trim(($this->sender_first_name ?? '') . ' ' . ($this->sender_last_name ?? '')),
            'customer_name' => trim(($this->customer_first_name ?? '') . ' ' . ($this->customer_last_name ?? '')),
            'customer_phone' => (string) $this->customer_phone,
            'customer_address' => (string) $this->customer_address,
            'from_state_id' => $this->from_state_id,
            'from_city_id' => $this->from_city_id,
            'to_state_id' => $this->to_state_id,
            'to_city_id' => $this->to_city_id,
            'cash_collection' => (string) $this->cash_collection,
            'delivery_charge' => (string) $this->delivery_charge,
            'total_delivery_amount' => (string) $this->total_delivery_amount,
            'distance_km' => (string) $this->distance_km,
            'created_at' => optional($this->created_at)->format('d M Y, h:i A'),
        ];
    }
}

