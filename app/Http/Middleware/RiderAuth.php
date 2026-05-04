<?php

namespace App\Http\Middleware;

use App\Enums\UserType;
use Closure;
use Illuminate\Http\Request;

class RiderAuth
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user || (int) $user->user_type !== UserType::DELIVERYMAN) {
            return response()->json([
                'success' => false,
                'message' => 'Rider authentication required.',
            ], 401);
        }

        return $next($request);
    }
}
