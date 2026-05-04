<?php

namespace App\Http\Resources\Frontend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                      => $this->id,
            'delivery_type_id'        => $this->delivery_type_id,
            'delivery_Type'           => __('deliveryType.'.$this->delivery_type_id),
            'shipping_type_id'        => $this->shipping_type_id,
            'shipping_type'           => $this->shippingType,
            'image'                   => $this->image,
            'description'             => $this->description,
            'position'                => $this->position,
            'status'                  => __('status.'.$this->status),
            'created_at'              => $this->created_at->format('d M Y, h:i A'),
            'updated_at'              => $this->updated_at->format('d M Y, h:i A'),
        ];
    }
}
