<?php

namespace App\Http\Resources\Frontend;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"                    => $this->id,
            "title"                 => $this->title,
            "slider"                => $this->slider,
            "slider_image"          => $this->slider_image,
            "small_title"           => $this->small_title,
            "status"                => $this->status,
            "statusName"            => __('status.'.$this->status),
            "position"              => $this->position,
            'created_at'            => $this->created_at->format('d M Y, h:i A'),
            'updated_at'            => $this->updated_at->format('d M Y, h:i A'),
        ];
    }
}
