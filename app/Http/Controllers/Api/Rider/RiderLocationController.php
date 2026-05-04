<?php

namespace App\Http\Controllers\Api\Rider;

use App\Http\Controllers\Controller;
use App\Services\MarketplaceParcelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RiderLocationController extends Controller
{
    public function store(Request $request, MarketplaceParcelService $service): JsonResponse
    {
        $request->validate([
            'parcel_id' => 'required|integer',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $riderId = (int) $request->user()->id;
        $result = $service->updateLocation(
            (int) $request->input('parcel_id'),
            $riderId,
            (float) $request->input('lat'),
            (float) $request->input('lng')
        );

        return response()->json($result['payload'], $result['status']);
    }
}

