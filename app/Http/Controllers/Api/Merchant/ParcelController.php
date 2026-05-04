<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Enums\DeliveryType;
use App\Enums\InvoiceStatus;
use App\Enums\ParcelStatus;
use App\Enums\UserType;
use App\Enums\WhoPays;
use App\Helpers\DeliveryChargeHelper;
use App\Http\Controllers\Controller;
use App\Models\Backend\MpesaPayment;
use App\Models\Backend\Parcel;
use App\Repositories\MerchantPanel\MerchantParcel\MerchantParcelInterface;
use App\Services\MarketplaceParcelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ParcelController extends Controller
{
    public function __construct(private MerchantParcelInterface $merchantParcelRepo)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user || (int) $user->user_type !== UserType::MERCHANT) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized user type for merchant parcel list API.',
            ], 403);
        }

        $merchant = $this->merchantParcelRepo->getMerchant((int) $user->id);
        if (!$merchant) {
            return response()->json([
                'success' => false,
                'message' => 'Merchant profile not found.',
            ], 404);
        }

        $validated = $request->validate([
            'status' => ['nullable', 'string', 'max:50'],
            'payment_status' => ['nullable', 'integer', 'in:' . InvoiceStatus::UNPAID . ',' . InvoiceStatus::PROCESSING . ',' . InvoiceStatus::PAID],
            'who_pays_either' => ['nullable', 'integer', 'in:' . WhoPays::SENDER . ',' . WhoPays::RECIPIENT . ',' . WhoPays::THIRD_PARTY],
            'search' => ['nullable', 'string', 'max:191'],
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $perPage = (int) ($validated['per_page'] ?? 15);
        $statusInput = strtolower(trim((string) ($validated['status'] ?? 'all')));

        $statusMap = [
            'pending' => ParcelStatus::MARKETPLACE_PENDING,
            'accepted' => ParcelStatus::MARKETPLACE_ACCEPTED,
            'picked_up' => ParcelStatus::MARKETPLACE_PICKED_UP,
            'delivered' => ParcelStatus::MARKETPLACE_DELIVERED,
            'cancelled' => ParcelStatus::MARKETPLACE_CANCELLED,
            'canceled' => ParcelStatus::MARKETPLACE_CANCELLED,
            'awaiting_sender_payment' => ParcelStatus::MARKETPLACE_SENDER_PAYMENT_AWAITING,
            'awaiting_receiver_payment' => ParcelStatus::MARKETPLACE_RECEIVER_PAYMENT_AWAITING,
        ];

        $query = Parcel::query()
            ->with(['deliveryman'])
            ->where('merchant_id', (int) $merchant->id)
            ->orderByDesc('id');

        if ($statusInput !== '' && $statusInput !== 'all') {
            if (isset($statusMap[$statusInput])) {
                $query->where('status', $statusMap[$statusInput]);
            } elseif (is_numeric($statusInput)) {
                $query->where('status', (int) $statusInput);
            }
        }

        if (isset($validated['payment_status'])) {
            $query->where('payment_status', (int) $validated['payment_status']);
        }

        if (isset($validated['who_pays_either'])) {
            $query->where('who_pays_either', (int) $validated['who_pays_either']);
        }

        if (!empty($validated['from_date'])) {
            $query->whereDate('created_at', '>=', (string) $validated['from_date']);
        }
        if (!empty($validated['to_date'])) {
            $query->whereDate('created_at', '<=', (string) $validated['to_date']);
        }

        if (!empty($validated['search'])) {
            $search = trim((string) $validated['search']);
            $query->where(function ($q) use ($search) {
                $q->where('tracking_id', 'like', '%' . $search . '%')
                    ->orWhere('customer_name', 'like', '%' . $search . '%')
                    ->orWhere('customer_phone', 'like', '%' . $search . '%')
                    ->orWhere('pickup_phone', 'like', '%' . $search . '%');
            });
        }

        $parcels = $query->paginate($perPage);
        $items = $parcels->getCollection()->map(function (Parcel $parcel) {
            $rider = $parcel->deliveryman;
            $trackingToken = (string) ($parcel->tracking_token ?? '');
            $status = (int) $parcel->status;
            $paymentStatus = (int) ($parcel->payment_status ?? 0);
            $whoPays = (int) ($parcel->who_pays_either ?? 0);
            $whoPaysForFlow = $whoPays === WhoPays::RECIPIENT ? WhoPays::RECIPIENT : WhoPays::SENDER;

            $paymentRequiredNow = $whoPaysForFlow === WhoPays::SENDER
                && $paymentStatus !== InvoiceStatus::PAID
                && in_array($status, [ParcelStatus::MARKETPLACE_SENDER_PAYMENT_AWAITING], true);

            $nextAction = 'wait_for_rider_acceptance';
            if ($status === ParcelStatus::MARKETPLACE_CANCELLED) {
                $nextAction = 'none';
            } elseif ($paymentStatus === InvoiceStatus::PROCESSING) {
                $nextAction = 'wait_for_payment_callback';
            } elseif (in_array($status, [
                ParcelStatus::MARKETPLACE_SENDER_PAYMENT_AWAITING,
                ParcelStatus::MARKETPLACE_RECEIVER_PAYMENT_AWAITING,
            ], true) && $paymentStatus !== InvoiceStatus::PAID) {
                $nextAction = 'complete_payment';
            }

            return [
                'id' => (int) $parcel->id,
                'tracking_id' => (string) ($parcel->tracking_id ?? ''),
                'tracking_token' => $trackingToken,
                'tracking_url' => $trackingToken !== '' ? url('/track/' . $trackingToken) : null,
                'status' => $status,
                'status_name' => $this->parcelStatusName($status),
                'payment_status' => $paymentStatus,
                'payment_status_name' => $this->paymentStatusName($paymentStatus),
                'payment_required_now' => $paymentRequiredNow,
                'next_action' => $nextAction,
                'who_pays_either' => $whoPays,
                'pickup_address' => (string) ($parcel->pickup_address ?? ''),
                'drop_address' => (string) ($parcel->customer_address ?? ''),
                'sender_phone' => (string) ($parcel->pickup_phone ?? ''),
                'receiver_name' => (string) ($parcel->customer_name ?? ''),
                'receiver_phone' => (string) ($parcel->customer_phone ?? ''),
                'distance_km' => (float) ($parcel->distance_km ?? 0),
                'weight' => (float) ($parcel->total_weight ?? $parcel->weight ?? 0),
                'delivery_charge' => (float) ($parcel->delivery_charge ?? 0),
                'final_paid_amount' => (float) ($parcel->final_paid_amount ?? 0),
                'rider' => $rider ? [
                    'id' => (int) $rider->id,
                    'name' => trim((string) (($rider->first_name ?? '') . ' ' . ($rider->last_name ?? ''))),
                    'phone' => (string) ($rider->mobile ?? ''),
                ] : null,
                'created_at' => optional($parcel->created_at)->toDateTimeString(),
                'updated_at' => optional($parcel->updated_at)->toDateTimeString(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $items,
            'meta' => [
                'current_page' => $parcels->currentPage(),
                'per_page' => $parcels->perPage(),
                'total' => $parcels->total(),
                'last_page' => $parcels->lastPage(),
            ],
            'links' => [
                'next' => $parcels->nextPageUrl(),
                'prev' => $parcels->previousPageUrl(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user || (int) $user->user_type !== UserType::MERCHANT) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized user type for merchant parcel create API.',
            ], 403);
        }

        $merchant = $this->merchantParcelRepo->getMerchant((int) $user->id);
        if (!$merchant) {
            return response()->json([
                'success' => false,
                'message' => 'Merchant profile not found.',
            ], 404);
        }

        $validated = $request->validate([
            'from_state_id' => ['required', 'integer', 'exists:provinces,id'],
            'to_state_id' => ['required', 'integer', 'exists:provinces,id'],
            'from_city_id' => ['required', 'integer', 'exists:cities,id'],
            'to_city_id' => ['required', 'integer', 'exists:cities,id'],
            'first_name' => ['required', 'string', 'max:191'],
            'last_name' => ['required', 'string', 'max:191'],
            'pickup_phone' => ['required', 'string', 'max:50'],
            'pickup_address' => ['required', 'string'],
            'pickup_lat' => ['required', 'numeric', 'between:-90,90'],
            'pickup_long' => ['required', 'numeric', 'between:-180,180'],
            'customer_first_name' => ['required', 'string', 'max:191'],
            'customer_last_name' => ['required', 'string', 'max:191'],
            'customer_phone' => ['required', 'string', 'max:50'],
            'customer_address' => ['required', 'string'],
            'drop_latitude' => ['required', 'numeric', 'between:-90,90'],
            'drop_longitude' => ['required', 'numeric', 'between:-180,180'],
            'category_id' => ['nullable', 'integer'],
            'package_type_id' => ['nullable', 'required_without:items'],
            'quantity' => ['nullable', 'numeric', 'min:1', 'required_without:items'],
            'who_pays_either' => ['required', 'integer', 'in:' . WhoPays::SENDER . ',' . WhoPays::RECIPIENT . ',' . WhoPays::THIRD_PARTY],
            'payment_intent' => ['nullable', 'string', 'in:pay_now,pay_later'],
            'distance_km' => ['required', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0', 'required_without:items'],
            'charge_details' => ['nullable', 'array'],
            'mpesa_checkout_request_id' => ['nullable', 'string'],
            'parcel_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:2048'],
            'items' => ['nullable', 'array', 'min:1'],
            'items.*.package_type_id' => ['required_with:items'],
            'items.*.quantity' => ['required_with:items', 'numeric', 'min:1'],
            'items.*.local_weight' => ['nullable', 'numeric', 'min:0'],
            'items.*.total_weight' => ['nullable', 'numeric', 'min:0'],
            'items.*.total_cbm' => ['nullable', 'numeric', 'min:0'],
            'items.*.length' => ['nullable', 'numeric', 'min:0'],
            'items.*.width' => ['nullable', 'numeric', 'min:0'],
            'items.*.height' => ['nullable', 'numeric', 'min:0'],
            'items.*.category_id' => ['nullable', 'integer'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.packaging_id' => ['nullable', 'integer'],
            'items.*.extra_cost' => ['nullable'],
            'items.*.extra_cost_amount' => ['nullable', 'numeric', 'min:0'],
            'items.*.extra_cost_description' => ['nullable', 'string'],
            'items.*.content_parcel' => ['nullable', 'string'],
            'items.*.unit_parcel_service_cost' => ['nullable', 'numeric', 'min:0'],
            'items.*.parcel_value' => ['nullable', 'numeric', 'min:0'],
        ]);

        $items = is_array($validated['items'] ?? null) ? $validated['items'] : [];
        $hasItems = count($items) > 0;

        $effectiveWeight = (float) ($validated['weight'] ?? 0);
        $effectiveTotalCbm = (float) $request->input('total_cbm', 0);
        $mainItem = [
            'package_type_id' => $validated['package_type_id'] ?? null,
            'quantity' => $validated['quantity'] ?? 1,
            'local_weight' => $effectiveWeight,
            'total_weight' => $effectiveWeight,
            'total_cbm' => $effectiveTotalCbm,
            'length' => $request->input('length'),
            'width' => $request->input('width'),
            'height' => $request->input('height'),
            'category_id' => $validated['category_id'] ?? null,
            'description' => $request->input('description'),
            'packaging_id' => $request->input('packaging_id'),
            'extra_cost' => $request->input('extra_cost'),
            'extra_cost_amount' => $request->input('extra_cost_amount'),
            'extra_cost_description' => $request->input('extra_cost_description'),
            'content_parcel' => $request->input('content_parcel'),
            'unit_parcel_service_cost' => $request->input('unit_parcel_service_cost'),
            'parcel_value' => $request->input('parcel_value'),
        ];

        if ($hasItems) {
            $effectiveWeight = 0.0;
            $effectiveTotalCbm = 0.0;

            foreach ($items as $item) {
                $qty = max((float) ($item['quantity'] ?? 1), 1.0);
                $localWeight = (float) ($item['local_weight'] ?? 0);
                $itemTotalWeight = array_key_exists('total_weight', $item)
                    ? (float) ($item['total_weight'] ?? 0)
                    : ($localWeight * $qty);
                $itemTotalCbm = (float) ($item['total_cbm'] ?? 0);

                $effectiveWeight += max($itemTotalWeight, 0);
                $effectiveTotalCbm += max($itemTotalCbm, 0);
            }

            if ($effectiveWeight <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Total weight must be greater than zero for items.',
                ], 422);
            }

            $mainItem = $items[0];
        }

        $whoPays = (int) $validated['who_pays_either'];
        $paymentIntent = (string) ($validated['payment_intent'] ?? 'pay_now');
        if ($whoPays === WhoPays::THIRD_PARTY && $paymentIntent === 'pay_later') {
            return response()->json([
                'success' => false,
                'message' => 'Pay later is not allowed for third person payer.',
            ], 422);
        }

        $whoPaysForPayment = $whoPays === WhoPays::RECIPIENT ? WhoPays::RECIPIENT : WhoPays::SENDER;
        if ($whoPaysForPayment === WhoPays::SENDER && $paymentIntent === 'pay_now') {
            $checkoutId = trim((string) ($validated['mpesa_checkout_request_id'] ?? ''));
            if ($checkoutId === '') {
                return response()->json([
                    'success' => false,
                    'message' => 'M-Pesa prompt is required before creating this parcel.',
                ], 422);
            }

            $payment = MpesaPayment::query()
                ->where('checkout_request_id', $checkoutId)
                ->where('merchant_id', (int) $user->id)
                ->first();

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'M-Pesa payment request not found. Please retry M-Pesa payment.',
                ], 422);
            }
        }

        $whoPaysForFlow = $whoPays === WhoPays::RECIPIENT ? WhoPays::RECIPIENT : WhoPays::SENDER;
        $computedBreakdown = DeliveryChargeHelper::instance()->marketplacePricingBreakdown(
            (float) $validated['distance_km'],
            $effectiveWeight,
            $whoPaysForFlow
        );
        $hasPricingConfig = ((float) ($computedBreakdown['base_fare'] ?? 0) > 0)
            || ((float) ($computedBreakdown['per_km_rate'] ?? 0) > 0)
            || ((float) ($computedBreakdown['per_kg_rate'] ?? 0) > 0);
        if (!$hasPricingConfig) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery charge is not configured for this route. Please configure pricing in admin panel.',
                'zone' => (string) ($computedBreakdown['zone'] ?? ''),
            ], 422);
        }
        if ((float) ($computedBreakdown['final'] ?? 0) <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Calculated delivery charge is zero. Please verify pricing, distance, and weight.',
                'zone' => (string) ($computedBreakdown['zone'] ?? ''),
            ], 422);
        }

        $chargeDetails = (array) ($validated['charge_details'] ?? []);
        $normalizedChargeDetails = [
            'vatTex' => (float) ($chargeDetails['vatTex'] ?? 0),
            'VatAmount' => (float) ($chargeDetails['VatAmount'] ?? 0),
            'deliveryChargeAmount' => (float) ($chargeDetails['deliveryChargeAmount'] ?? $computedBreakdown['base'] ?? 0),
            'totalDeliveryChargeAmount' => (float) ($chargeDetails['totalDeliveryChargeAmount'] ?? $computedBreakdown['final'] ?? 0),
            'currentPayable' => (float) ($chargeDetails['currentPayable'] ?? $computedBreakdown['final'] ?? 0),
            'scheduledServiceAmount' => (float) ($chargeDetails['scheduledServiceAmount'] ?? 0),
            'totalExtraCost' => (float) ($chargeDetails['totalExtraCost'] ?? 0),
            'packagingAmount' => (float) ($chargeDetails['packagingAmount'] ?? 0),
            'liquidFragileAmount' => (float) ($chargeDetails['liquidFragileAmount'] ?? 0),
        ];

        $request->merge([
            'delivery_type_id' => (int) $validated['from_state_id'] === (int) $validated['to_state_id']
                ? DeliveryType::SAMEDAY
                : DeliveryType::SUBCITY,
            'from_point_type' => (int) $request->input('from_point_type', 1),
            'to_point_type' => (int) $request->input('to_point_type', 1),
            'payment_intent' => $paymentIntent,
            'who_pays_either' => $whoPays,
            'chargeDetails' => json_encode($normalizedChargeDetails),
            'category_id' => $validated['category_id'] ?? ($mainItem['category_id'] ?? null),
            'package_type_id' => $mainItem['package_type_id'] ?? ($validated['package_type_id'] ?? null),
            'quantity' => (float) ($mainItem['quantity'] ?? ($validated['quantity'] ?? 1)),
            'length' => $mainItem['length'] ?? null,
            'width' => $mainItem['width'] ?? null,
            'height' => $mainItem['height'] ?? null,
            'main_description' => $mainItem['description'] ?? null,
            'main_category_id' => $mainItem['category_id'] ?? null,
            'main_unit_parcel_service_cost' => (float) ($mainItem['unit_parcel_service_cost'] ?? 0),
            'packaging_id' => $mainItem['packaging_id'] ?? null,
            'extra_cost' => $mainItem['extra_cost'] ?? null,
            'extra_cost_amount' => (float) ($mainItem['extra_cost_amount'] ?? 0),
            'extra_cost_description' => $mainItem['extra_cost_description'] ?? null,
            'content_parcel' => $mainItem['content_parcel'] ?? null,
            'weight' => $effectiveWeight,
            'total_weight' => $effectiveWeight,
            'local_weight' => (float) ($mainItem['local_weight'] ?? $effectiveWeight),
            'main_total_weight' => $effectiveWeight,
            // Keep mobile payload minimal; repository expects CBM totals.
            'total_cbm' => $effectiveTotalCbm,
            'main_total_cbm' => $effectiveTotalCbm,
            'total_valumetric_weight' => (float) $request->input('total_valumetric_weight', 0),
            'items' => $items,
            'payment_confirmed' => $whoPaysForPayment === WhoPays::SENDER && $paymentIntent === 'pay_now',
        ]);

        $saved = $this->merchantParcelRepo->store($request, (int) $merchant->id);
        if (!$saved) {
            $reason = (string) ($this->merchantParcelRepo->getLastError() ?? 'Unknown error');
            Log::error('Merchant API parcel create failed', [
                'merchant_user_id' => (int) $user->id,
                'reason' => $reason,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create parcel.',
                'error' => $reason,
            ], 500);
        }

        $parcel = Parcel::query()
            ->where('merchant_id', (int) $merchant->id)
            ->latest('id')
            ->first();

        return response()->json([
            'success' => true,
            'message' => 'Parcel created successfully.',
            'data' => [
                'id' => (int) $parcel->id,
                'tracking_id' => (string) $parcel->tracking_id,
                'tracking_token' => (string) ($parcel->tracking_token ?? ''),
                'status' => (int) $parcel->status,
                'status_name' => $this->parcelStatusName((int) $parcel->status),
                'payment_status' => (int) $parcel->payment_status,
                'payment_status_name' => $this->paymentStatusName((int) $parcel->payment_status),
                'payment_required_now' => $whoPaysForPayment === WhoPays::SENDER && $paymentIntent === 'pay_now',
                'next_action' => $parcel->payment_status === InvoiceStatus::PAID || $parcel->status === ParcelStatus::MARKETPLACE_PENDING
                    ? 'wait_for_rider_acceptance'
                    : 'complete_payment',
            ],
        ]);
    }

    public function tracking(Request $request, Parcel $parcel, MarketplaceParcelService $service): JsonResponse
    {
        $user = $request->user();
        if (!$user || (int) $user->user_type !== UserType::MERCHANT) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized user type for merchant tracking API.',
            ], 403);
        }

        $merchant = $this->merchantParcelRepo->getMerchant((int) $user->id);
        if (!$merchant || (int) $parcel->merchant_id !== (int) $merchant->id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have access to this parcel.',
            ], 403);
        }

        $trackingToken = (string) ($parcel->tracking_token ?? '');
        if ($trackingToken === '') {
            return response()->json([
                'success' => false,
                'message' => 'Tracking is not available yet for this parcel.',
            ], 422);
        }

        $result = $service->trackingData($trackingToken);
        if (($result['status'] ?? 500) !== 200) {
            return response()->json($result['payload'] ?? ['success' => false, 'message' => 'Tracking data not found.'], (int) ($result['status'] ?? 500));
        }

        $payload = (array) ($result['payload'] ?? []);
        $data = (array) data_get($payload, 'data', []);
        $data['parcel_id'] = (int) $parcel->id;
        $data['tracking_id'] = (string) ($parcel->tracking_id ?? '');
        $data['tracking_token'] = $trackingToken;

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    private function parcelStatusName(int $status): string
    {
        $marketplaceMap = [
            ParcelStatus::MARKETPLACE_PENDING => 'pending',
            ParcelStatus::MARKETPLACE_ACCEPTED => 'accepted',
            ParcelStatus::MARKETPLACE_PICKED_UP => 'picked_up',
            ParcelStatus::MARKETPLACE_DELIVERED => 'delivered',
            ParcelStatus::MARKETPLACE_CANCELLED => 'cancelled',
            ParcelStatus::MARKETPLACE_SENDER_PAYMENT_AWAITING => 'awaiting_sender_payment',
            ParcelStatus::MARKETPLACE_RECEIVER_PAYMENT_AWAITING => 'awaiting_receiver_payment',
        ];
        if (isset($marketplaceMap[$status])) {
            return $marketplaceMap[$status];
        }

        $key = 'parcelStatus.' . $status;
        $translated = trans($key);
        return $translated !== $key ? (string) $translated : (string) $status;
    }

    private function paymentStatusName(int $paymentStatus): string
    {
        return match ($paymentStatus) {
            InvoiceStatus::UNPAID => 'unpaid',
            InvoiceStatus::PROCESSING => 'processing',
            InvoiceStatus::PAID => 'paid',
            default => 'unknown',
        };
    }
}
