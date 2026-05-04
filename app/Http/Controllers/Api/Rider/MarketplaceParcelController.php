<?php

namespace App\Http\Controllers\Api\Rider;

use App\Http\Controllers\Controller;
use App\Http\Resources\Rider\ActiveParcelResource;
use App\Http\Resources\Rider\MarketplaceParcelResource;
use App\Http\Resources\Rider\ParcelDetailResource;
use App\Models\Backend\Parcel;
use App\Enums\ParcelStatus;
use App\Services\MarketplaceParcelService;
use App\Http\Services\SmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MarketplaceParcelController extends Controller
{
    public function index(Request $request, MarketplaceParcelService $service): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        if ($perPage < 1) {
            $perPage = 15;
        }
        if ($perPage > 100) {
            $perPage = 100;
        }

        $deliveryMan = $request->user() ? $request->user()->deliveryman : null;
        if (!$deliveryMan || (int) $deliveryMan->is_available !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Rider is offline.',
            ], 403);
        }

        $filters = $request->only([
            'from_state_id',
            'to_state_id',
            'from_city_id',
            'to_city_id',
            'min_distance_km',
            'max_distance_km',
            'min_cash_collection',
            'max_cash_collection',
            'created_from',
            'created_to',
        ]);

        $parcels = $service->availableParcels($filters, $perPage);

        return MarketplaceParcelResource::collection($parcels)
            ->additional(['success' => true])
            ->response();
    }

    public function active(Request $request, MarketplaceParcelService $service): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        if ($perPage < 1) {
            $perPage = 15;
        }
        if ($perPage > 100) {
            $perPage = 100;
        }

        $riderId = (int) $request->user()->id;
        $parcels = $service->activeParcels($riderId, $perPage);

        return ActiveParcelResource::collection($parcels)
            ->additional(['success' => true])
            ->response();
    }

    public function delivered(Request $request, MarketplaceParcelService $service): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        if ($perPage < 1) {
            $perPage = 15;
        }
        if ($perPage > 100) {
            $perPage = 100;
        }

        $riderId = (int) $request->user()->id;
        $parcels = $service->deliveredParcels($riderId, $perPage);

        return MarketplaceParcelResource::collection($parcels)
            ->additional(['success' => true])
            ->response();
    }

    public function listByStatus(Request $request, MarketplaceParcelService $service): JsonResponse
    {
        $request->validate([
            'status' => 'required|string|in:accepted,picked_up,delivered,canceled,all',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $perPage = (int) $request->query('per_page', 15);
        if ($perPage < 1) {
            $perPage = 15;
        }
        if ($perPage > 100) {
            $perPage = 100;
        }

        $status = (string) $request->query('status');
        $riderId = (int) $request->user()->id;
        $parcels = $service->riderParcelsByStatus($riderId, $status, $perPage);

        return MarketplaceParcelResource::collection($parcels)
            ->additional([
                'success' => true,
                'status_filter' => $status,
            ])
            ->response();
    }

    public function accept(Request $request, Parcel $parcel, MarketplaceParcelService $service): JsonResponse
    {
        $deliveryMan = $request->user() ? $request->user()->deliveryman : null;
        if (!$deliveryMan || (int) $deliveryMan->is_available !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Rider is offline.',
            ], 403);
        }

        $riderId = (int) $request->user()->id;
        $result = $service->accept($parcel, $riderId);

        return response()->json($result['payload'], $result['status']);
    }

    public function cancel(Request $request, Parcel $parcel, MarketplaceParcelService $service): JsonResponse
    {
        $riderId = (int) $request->user()->id;
        $result = $service->cancel($parcel, $riderId);

        return response()->json($result['payload'], $result['status']);
    }

    public function show(Request $request, Parcel $parcel): JsonResponse
    {
        $riderId = (int) $request->user()->id;
        $isAvailable = empty($parcel->delivery_man_id) && in_array((int) $parcel->status, [
            ParcelStatus::MARKETPLACE_PENDING,
            ParcelStatus::MARKETPLACE_SENDER_PAYMENT_AWAITING,
            ParcelStatus::MARKETPLACE_RECEIVER_PAYMENT_AWAITING,
        ], true);
        $isAssignedToRider = (int) $parcel->delivery_man_id === $riderId;

        if (!$isAvailable && !$isAssignedToRider) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this parcel.',
            ], 403);
        }

        return (new ParcelDetailResource($parcel))
            ->additional(['success' => true])
            ->response();
    }

    public function status(Request $request, Parcel $parcel, MarketplaceParcelService $service): JsonResponse
    {
        $request->validate([
            'status' => 'required|string|in:MARKETPLACE_PICKED_UP,MARKETPLACE_DELIVERED',
        ]);

        $statusKey = strtoupper((string) $request->input('status'));
        $statusMap = [
            'MARKETPLACE_PICKED_UP' => ParcelStatus::MARKETPLACE_PICKED_UP,
            'MARKETPLACE_DELIVERED' => ParcelStatus::MARKETPLACE_DELIVERED,
        ];

        if (!isset($statusMap[$statusKey])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status value.',
            ], 422);
        }

        $riderId = (int) $request->user()->id;
        $result = $service->updateStatus($parcel, $riderId, $statusMap[$statusKey]);

        return response()->json($result['payload'], $result['status']);
    }

    public function sendOtp(Request $request, Parcel $parcel, MarketplaceParcelService $service): JsonResponse
    {
        $riderId = (int) $request->user()->id;
        $result = $service->sendReceiverOtp($parcel, $riderId);

        return response()->json($result['payload'], $result['status']);
    }

    public function verifyOtp(Request $request, Parcel $parcel, MarketplaceParcelService $service): JsonResponse
    {
        $request->validate([
            'otp' => ['required', 'numeric', 'digits:6'],
        ]);

        $riderId = (int) $request->user()->id;
        $otp = (string) $request->input('otp');

        $result = $service->verifyReceiverOtp($parcel, $riderId, $otp);

        return response()->json($result['payload'], $result['status']);
    }

    public function sendPickupOtp(Request $request, Parcel $parcel, MarketplaceParcelService $service): JsonResponse
    {
        $riderId = (int) $request->user()->id;
        $result = $service->sendPickupOtp($parcel, $riderId);

        return response()->json($result['payload'], $result['status']);
    }

    public function verifyPickupOtp(Request $request, Parcel $parcel, MarketplaceParcelService $service): JsonResponse
    {
        $request->validate([
            'otp' => ['required', 'numeric', 'digits:6'],
        ]);

        $riderId = (int) $request->user()->id;
        $otp = (string) $request->input('otp');

        $result = $service->verifyPickupOtp($parcel, $riderId, $otp);

        return response()->json($result['payload'], $result['status']);
    }
}

