<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Enums\InvoiceStatus;
use App\Enums\ParcelStatus;
use App\Enums\WhoPays;
use App\Http\Controllers\Controller;
use App\Models\Backend\MpesaPayment;
use App\Models\Backend\Parcel;
use App\Services\MpesaStkPushService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function history(Request $request): JsonResponse
    {
        $merchantUserId = (int) optional($request->user())->id;
        $perPage = (int) $request->query('per_page', 15);
        if ($perPage < 1) {
            $perPage = 15;
        }
        if ($perPage > 100) {
            $perPage = 100;
        }

        $query = MpesaPayment::query()
            ->with(['parcel'])
            ->where(function ($q) use ($merchantUserId) {
                $q->where('merchant_id', $merchantUserId)
                    ->orWhereHas('parcel', function ($parcelQuery) use ($merchantUserId) {
                        $parcelQuery->whereHas('merchant', function ($merchantQuery) use ($merchantUserId) {
                            $merchantQuery->where('user_id', $merchantUserId);
                        });
                    });
            })
            ->orderByDesc('id');

        $validated = $request->validate([
            'status' => ['nullable', 'string', 'in:pending,success,failed'],
            'payer' => ['nullable', 'string', 'in:sender,receiver,third_person'],
            'search' => ['nullable', 'string', 'max:191'],
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date'],
        ]);

        if (!empty($validated['status'])) {
            $query->where('status', (string) $validated['status']);
        }

        if (!empty($validated['from_date'])) {
            $from = Carbon::parse((string) $validated['from_date'])->startOfDay();
            $query->where('created_at', '>=', $from);
        }
        if (!empty($validated['to_date'])) {
            $to = Carbon::parse((string) $validated['to_date'])->endOfDay();
            $query->where('created_at', '<=', $to);
        }

        if (!empty($validated['payer'])) {
            $query->where(function ($q) use ($validated) {
                $payer = (string) $validated['payer'];
                if ($payer === 'receiver') {
                    $q->where('parcel_payload->who_pays', 'receiver')
                        ->orWhereHas('parcel', function ($parcelQuery) {
                            $parcelQuery->where('who_pays_either', WhoPays::RECIPIENT);
                        });
                } elseif ($payer === 'third_person') {
                    $q->whereHas('parcel', function ($parcelQuery) {
                        $parcelQuery->where('who_pays_either', WhoPays::THIRD_PARTY);
                    });
                } else {
                    $q->where('parcel_payload->who_pays', 'sender')
                        ->orWhereNull('parcel_id')
                        ->orWhereHas('parcel', function ($parcelQuery) {
                            $parcelQuery->whereIn('who_pays_either', [WhoPays::SENDER, WhoPays::THIRD_PARTY]);
                        });
                }
            });
        }

        if (!empty($validated['search'])) {
            $search = trim((string) $validated['search']);
            $query->where(function ($q) use ($search) {
                $q->where('checkout_request_id', 'like', '%' . $search . '%')
                    ->orWhere('merchant_request_id', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhereHas('parcel', function ($parcelQuery) use ($search) {
                        $parcelQuery->where('tracking_id', 'like', '%' . $search . '%');
                    });
            });
        }

        $payments = $query->paginate($perPage);
        $items = $payments->getCollection()->map(function (MpesaPayment $payment) {
            $parcel = $payment->parcel;
            $payloadWhoPays = (string) data_get($payment->parcel_payload, 'who_pays', '');
            $whoPaysEither = (int) ($parcel->who_pays_either ?? 0);
            $payerLabel = 'sender';
            if ($whoPaysEither === WhoPays::RECIPIENT || $payloadWhoPays === 'receiver') {
                $payerLabel = 'receiver';
            } elseif ($whoPaysEither === WhoPays::THIRD_PARTY) {
                $payerLabel = 'third_person';
            }

            return [
                'id' => (int) $payment->id,
                'parcel_id' => $payment->parcel_id ? (int) $payment->parcel_id : null,
                'tracking_id' => $parcel ? (string) ($parcel->tracking_id ?? '') : '',
                'who_pays_either' => $whoPaysEither ?: null,
                'payer' => $payerLabel,
                'phone' => (string) ($payment->phone ?? ''),
                'amount' => (float) ($payment->amount ?? 0),
                'status' => (string) ($payment->status ?? ''),
                'checkout_request_id' => (string) ($payment->checkout_request_id ?? ''),
                'merchant_request_id' => (string) ($payment->merchant_request_id ?? ''),
                'created_at' => optional($payment->created_at)->toDateTimeString(),
                'updated_at' => optional($payment->updated_at)->toDateTimeString(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $items,
            'meta' => [
                'current_page' => $payments->currentPage(),
                'per_page' => $payments->perPage(),
                'total' => $payments->total(),
                'last_page' => $payments->lastPage(),
            ],
            'links' => [
                'next' => $payments->nextPageUrl(),
                'prev' => $payments->previousPageUrl(),
            ],
        ]);
    }

    public function prompt(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
            'amount' => ['required', 'numeric', 'min:1'],
            'account_reference' => ['nullable', 'string', 'max:191'],
            'transaction_desc' => ['nullable', 'string', 'max:191'],
            'parcel_id' => ['nullable', 'integer', 'exists:parcels,id'],
        ]);

        if (!empty($validated['parcel_id'])) {
            $parcel = Parcel::query()->find((int) $validated['parcel_id']);
            if (!$parcel || !$this->isOwnedByMerchantUser($parcel, (int) optional($request->user())->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not allowed to pay for this parcel.',
                ], 403);
            }
        }

        $result = app(MpesaStkPushService::class)->initiate([
            'merchant_id' => (int) optional($request->user())->id,
            'phone' => (string) $validated['phone'],
            'amount' => (float) $validated['amount'],
            'account_reference' => (string) ($validated['account_reference'] ?? 'Marketplace Parcel'),
            'transaction_desc' => (string) ($validated['transaction_desc'] ?? 'Marketplace parcel payment'),
            // Important: for pre-create prompts, never auto-create parcel from callback.
            'parcel_payload' => [
                'create_on_callback' => 0,
                'flow' => 'merchant_app_precreate',
            ],
        ]);

        if (empty($result['success'])) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to send M-Pesa prompt.',
            ], 422);
        }

        $payload = (array) ($result['payload'] ?? []);
        return response()->json([
            'success' => true,
            'message' => 'M-Pesa prompt sent.',
            'data' => [
                'checkout_request_id' => (string) ($payload['CheckoutRequestID'] ?? ''),
                'merchant_request_id' => (string) ($payload['MerchantRequestID'] ?? ''),
                'response_code' => (string) ($payload['ResponseCode'] ?? ''),
                'response_description' => (string) ($payload['ResponseDescription'] ?? ''),
                'customer_message' => (string) ($payload['CustomerMessage'] ?? ''),
            ],
        ]);
    }

    public function pay(Request $request, Parcel $parcel): JsonResponse
    {
        $merchantUserId = (int) optional($request->user())->id;
        if (!$this->isOwnedByMerchantUser($parcel, $merchantUserId)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not allowed to pay for this parcel.',
            ], 403);
        }

        if (in_array((int) $parcel->status, [ParcelStatus::MARKETPLACE_DELIVERED, ParcelStatus::MARKETPLACE_CANCELLED], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Payment cannot be updated for delivered/cancelled parcels.',
            ], 422);
        }

        if ((int) ($parcel->payment_status ?? 0) === InvoiceStatus::PAID) {
            return response()->json([
                'success' => false,
                'message' => 'This parcel is already paid.',
            ], 422);
        }

        $validated = $request->validate([
            'who_pays_either' => ['required', 'integer', 'in:' . WhoPays::SENDER . ',' . WhoPays::RECIPIENT . ',' . WhoPays::THIRD_PARTY],
            'phone' => ['required', 'string', 'max:30'],
            'account_reference' => ['nullable', 'string', 'max:191'],
            'transaction_desc' => ['nullable', 'string', 'max:191'],
        ]);

        $whoPays = (int) $validated['who_pays_either'];
        $whoPaysForFlow = $this->whoPaysForFlow($whoPays);
        $amount = (float) ($parcel->final_paid_amount ?? $parcel->total_delivery_amount ?? 0);
        if ($amount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid parcel payable amount for M-Pesa.',
            ], 422);
        }

        $parcel->who_pays_either = $whoPays;
        if ($whoPaysForFlow === WhoPays::RECIPIENT) {
            $parcel->receiver_mpesa_phone = (string) $validated['phone'];
        }
        $parcel->save();

        $result = app(MpesaStkPushService::class)->initiate([
            'merchant_id' => $merchantUserId,
            'parcel_id' => (int) $parcel->id,
            'phone' => (string) $validated['phone'],
            'amount' => $amount,
            'account_reference' => (string) ($validated['account_reference']
                ?? ($whoPaysForFlow === WhoPays::RECIPIENT ? 'Receiver Payment' : 'Sender Payment')),
            'transaction_desc' => (string) ($validated['transaction_desc']
                ?? (($whoPaysForFlow === WhoPays::RECIPIENT ? 'Receiver' : 'Sender') . ' payment for parcel ' . $parcel->tracking_id)),
            'parcel_payload' => [
                'parcel_id' => (int) $parcel->id,
                'who_pays' => $whoPaysForFlow === WhoPays::RECIPIENT ? 'receiver' : 'sender',
            ],
        ]);

        if (empty($result['success'])) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to send M-Pesa prompt.',
            ], 422);
        }

        $parcel->payment_status = InvoiceStatus::PROCESSING;
        if (in_array((int) $parcel->status, [
            ParcelStatus::MARKETPLACE_SENDER_PAYMENT_AWAITING,
            ParcelStatus::MARKETPLACE_RECEIVER_PAYMENT_AWAITING,
        ], true)) {
            $parcel->status = ParcelStatus::MARKETPLACE_PENDING;
        }
        $parcel->save();

        $payload = (array) ($result['payload'] ?? []);
        return response()->json([
            'success' => true,
            'message' => 'M-Pesa prompt sent for this parcel.',
            'data' => [
                'parcel_id' => (int) $parcel->id,
                'status' => (int) $parcel->status,
                'payment_status' => (int) $parcel->payment_status,
                'checkout_request_id' => (string) ($payload['CheckoutRequestID'] ?? ''),
                'merchant_request_id' => (string) ($payload['MerchantRequestID'] ?? ''),
            ],
        ]);
    }

    public function updatePayer(Request $request, Parcel $parcel): JsonResponse
    {
        $merchantUserId = (int) optional($request->user())->id;
        if (!$this->isOwnedByMerchantUser($parcel, $merchantUserId)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not allowed to update this parcel.',
            ], 403);
        }

        if (in_array((int) $parcel->status, [ParcelStatus::MARKETPLACE_DELIVERED, ParcelStatus::MARKETPLACE_CANCELLED], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Payer cannot be changed for delivered/cancelled parcels.',
            ], 422);
        }

        if ((int) ($parcel->payment_status ?? 0) === InvoiceStatus::PAID) {
            return response()->json([
                'success' => false,
                'message' => 'This parcel is already paid.',
            ], 422);
        }

        $validated = $request->validate([
            'who_pays_either' => ['required', 'integer', 'in:' . WhoPays::SENDER . ',' . WhoPays::RECIPIENT . ',' . WhoPays::THIRD_PARTY],
            'payment_intent' => ['required', 'string', 'in:pay_now,pay_later'],
            'phone' => ['nullable', 'string', 'max:30'],
            'trigger_prompt' => ['nullable', 'boolean'],
        ]);

        $whoPays = (int) $validated['who_pays_either'];
        $paymentIntent = (string) $validated['payment_intent'];
        if ($whoPays === WhoPays::THIRD_PARTY && $paymentIntent === 'pay_later') {
            return response()->json([
                'success' => false,
                'message' => 'Pay later is not allowed for third person payer.',
            ], 422);
        }

        $whoPaysForFlow = $this->whoPaysForFlow($whoPays);
        $awaitingStatus = $whoPaysForFlow === WhoPays::RECIPIENT
            ? ParcelStatus::MARKETPLACE_RECEIVER_PAYMENT_AWAITING
            : ParcelStatus::MARKETPLACE_SENDER_PAYMENT_AWAITING;

        $parcel->who_pays_either = $whoPays;
        $parcel->payment_status = InvoiceStatus::UNPAID;
        $parcel->status = $awaitingStatus;
        if ($whoPaysForFlow === WhoPays::RECIPIENT && !empty($validated['phone'])) {
            $parcel->receiver_mpesa_phone = (string) $validated['phone'];
        }
        $parcel->save();

        if ($paymentIntent === 'pay_later') {
            return response()->json([
                'success' => true,
                'message' => 'Payer updated. Parcel is awaiting payment.',
                'data' => [
                    'parcel_id' => (int) $parcel->id,
                    'status' => (int) $parcel->status,
                    'payment_status' => (int) $parcel->payment_status,
                    'next_action' => 'complete_payment',
                ],
            ]);
        }

        $triggerPrompt = (bool) ($validated['trigger_prompt'] ?? true);
        if (!$triggerPrompt) {
            return response()->json([
                'success' => true,
                'message' => 'Payer updated. Prompt not triggered.',
                'data' => [
                    'parcel_id' => (int) $parcel->id,
                    'status' => (int) $parcel->status,
                    'payment_status' => (int) $parcel->payment_status,
                    'next_action' => 'send_mpesa_prompt',
                ],
            ]);
        }

        $phone = trim((string) ($validated['phone'] ?? ''));
        if ($phone === '') {
            return response()->json([
                'success' => false,
                'message' => 'Phone is required to trigger M-Pesa prompt for pay_now.',
            ], 422);
        }

        $amount = (float) ($parcel->final_paid_amount ?? $parcel->total_delivery_amount ?? 0);
        if ($amount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid parcel payable amount for M-Pesa.',
            ], 422);
        }

        $result = app(MpesaStkPushService::class)->initiate([
            'merchant_id' => $merchantUserId,
            'parcel_id' => (int) $parcel->id,
            'phone' => $phone,
            'amount' => $amount,
            'account_reference' => $whoPaysForFlow === WhoPays::RECIPIENT ? 'Receiver Payment' : 'Sender Payment',
            'transaction_desc' => ($whoPaysForFlow === WhoPays::RECIPIENT ? 'Receiver' : 'Sender') . ' payment for parcel ' . $parcel->tracking_id,
            'parcel_payload' => [
                'parcel_id' => (int) $parcel->id,
                'who_pays' => $whoPaysForFlow === WhoPays::RECIPIENT ? 'receiver' : 'sender',
            ],
        ]);

        if (empty($result['success'])) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to send M-Pesa prompt.',
            ], 422);
        }

        $parcel->payment_status = InvoiceStatus::PROCESSING;
        $parcel->status = ParcelStatus::MARKETPLACE_PENDING;
        $parcel->save();

        $payload = (array) ($result['payload'] ?? []);
        return response()->json([
            'success' => true,
            'message' => 'Payer updated and M-Pesa prompt sent.',
            'data' => [
                'parcel_id' => (int) $parcel->id,
                'status' => (int) $parcel->status,
                'payment_status' => (int) $parcel->payment_status,
                'checkout_request_id' => (string) ($payload['CheckoutRequestID'] ?? ''),
                'merchant_request_id' => (string) ($payload['MerchantRequestID'] ?? ''),
            ],
        ]);
    }

    private function isOwnedByMerchantUser(Parcel $parcel, int $merchantUserId): bool
    {
        $ownerUserId = (int) optional(optional($parcel->merchant)->user)->id;
        return $ownerUserId > 0 && $ownerUserId === $merchantUserId;
    }

    private function whoPaysForFlow(int $whoPays): int
    {
        return $whoPays === WhoPays::RECIPIENT ? WhoPays::RECIPIENT : WhoPays::SENDER;
    }
}
