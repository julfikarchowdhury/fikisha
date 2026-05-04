<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MarketplaceParcelService;
use Illuminate\Http\JsonResponse;

class TrackingController extends Controller
{
    public function show(string $trackingToken, MarketplaceParcelService $service): JsonResponse
    {
        $result = $service->trackingData($trackingToken);

        return response()->json($result['payload'], $result['status']);
    }
}

