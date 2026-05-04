<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Backend\City;
use App\Models\Backend\Province;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    public function states(): JsonResponse
    {
        $states = Province::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json([
            'success' => true,
            'states' => $states,
        ]);
    }

    public function cities(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'state_id' => ['required', 'integer', 'exists:provinces,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $cities = City::query()
            ->where('province_id', (int) $request->input('state_id'))
            ->orderBy('name')
            ->get(['id', 'province_id', 'name', 'portal_code']);

        return response()->json([
            'success' => true,
            'cities' => $cities,
        ]);
    }
}
