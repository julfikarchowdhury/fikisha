<?php

namespace App\Http\Resources\v10\Collection;

use App\Http\Resources\v10\ParcelResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ParcelCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => ParcelResource::collection($this->collection),
            'meta' => [
                'total' => $this->collection->count(),
            ],
        ];
    }

    
}
