<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RiderLocationUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $trackingToken;
    public float $lat;
    public float $lng;
    public int $status;
    public string $updatedAt;

    public function __construct(string $trackingToken, float $lat, float $lng, int $status, string $updatedAt)
    {
        $this->trackingToken = $trackingToken;
        $this->lat = $lat;
        $this->lng = $lng;
        $this->status = $status;
        $this->updatedAt = $updatedAt;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('tracking.' . $this->trackingToken);
    }

    public function broadcastAs(): string
    {
        return 'rider.location.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'lat' => $this->lat,
            'lng' => $this->lng,
            'status' => $this->status,
            'updated_at' => $this->updatedAt,
        ];
    }
}

