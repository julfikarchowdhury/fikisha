<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrackingPageController extends Controller
{
    public function show(Request $request, string $trackingToken)
    {
        $pusher = config('broadcasting.connections.pusher');

        return view('frontend.pages.tracking_live', [
            'trackingToken' => $trackingToken,
            'pusherKey' => $pusher['key'] ?? null,
            'pusherCluster' => $pusher['options']['cluster'] ?? null,
        ]);
    }
}

