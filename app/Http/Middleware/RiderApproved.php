<?php

namespace App\Http\Middleware;

use App\Enums\RiderStatus;
use App\Enums\Status;
use Closure;
use Illuminate\Http\Request;

class RiderApproved
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $deliveryMan = $user ? $user->deliveryman : null;

        if (!$user || !$deliveryMan) {
            return response()->json([
                'success' => false,
                'message' => 'Rider profile not found.',
            ], 403);
        }

        if ((int) $deliveryMan->rider_status !== RiderStatus::APPROVED) {
            return response()->json([
                'success' => false,
                'message' => 'Rider account is not approved yet.',
            ], 403);
        }

        return $next($request);
    }
}
