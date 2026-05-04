<?php

namespace App\Http\Resources\Frontend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartnerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'image'         => $this->image,
            'link'          => $this->link,
            'position'      => $this->position,
            'status'        => __('status.'.$this->status),
            'created_at'    => $this->created_at->format('d M Y, h:i A'),
            'updated_at'    => $this->updated_at->format('d M Y, h:i A'),
        ];
    }
}
