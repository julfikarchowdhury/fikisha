<?php

namespace App\Http\Controllers\Backend\MerchantPanel;

use App\Http\Controllers\Controller;
use App\Models\Backend\MpesaPayment;
use App\Models\Backend\Parcel;
use App\Enums\InvoiceStatus;
use App\Enums\ParcelStatus;
use App\Enums\WhoPays;
use App\Repositories\MerchantPanel\MerchantParcel\MerchantParcelInterface;
use App\Repositories\Merchant\MerchantInterface;
use App\Services\MpesaStkPushService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaController extends Controller
{
    protected MerchantParcelInterface $parcelRepo;
    protected MerchantInterface $merchantRepo;

    public function __construct(MerchantParcelInterface $parcelRepo, MerchantInterface $merchantRepo)
    {
        $this->parcelRepo = $parcelRepo;
        $this->merchantRepo = $merchantRepo;
    }

    public function pay(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'phone' => 'required|string',
            'parcel_payload' => 'required|string',
        ]);

        $parcelPayload = json_decode($request->parcel_payload, true);
        if (!is_array($parcelPayload)) {
            Toastr::error('Invalid parcel payload.', __('message.error'));
            return redirect()->back();
        }
        $backInput = array_merge($parcelPayload, [
            'payment_intent' => $parcelPayload['payment_intent'] ?? 'pay_now',
            'who_pays_either' => $parcelPayload['who_pays_either'] ?? null,
        ]);

        $consumerKey = globalSettings('mpesa_consumer_key');
        $consumerSecret = globalSettings('mpesa_consumer_secret');
        $shortcode = globalSettings('mpesa_shortcode');
        $passkey = globalSettings('mpesa_passkey');
        $callbackUrl = globalSettings('mpesa_callback_url');
        $environment = globalSettings('mpesa_environment') ?: 'sandbox';

        if (!$consumerKey || !$consumerSecret || !$shortcode || !$callbackUrl) {
            Toastr::error('M-Pesa settings are incomplete.', __('message.error'));
            return redirect()->back()->withInput($backInput);
        }

        if ($environment === 'live' && !$passkey) {
            Toastr::error('M-Pesa Passkey is required for production.', __('message.error'));
            return redirect()->back()->withInput($backInput);
        }

        $phone = $this->normalizePhone($request->phone);
        if (!$phone) {
            Toastr::error('Invalid M-Pesa phone number.', __('message.error'));
            return redirect()->back()->withInput($backInput);
        }

        $amount = (int) ceil((float) $request->amount);
        if ($amount <= 0) {
            Toastr::error('Invalid payment amount.', __('message.error'));
            return redirect()->back()->withInput($backInput);
        }

        $baseUrl = $environment === 'live' ? 'https://api.safaricom.co.ke' : 'https://sandbox.safaricom.co.ke';

        $tokenResponse = Http::withBasicAuth($consumerKey, $consumerSecret)
            ->timeout(15)
            ->connectTimeout(10)
            ->get($baseUrl . '/oauth/v1/generate', ['grant_type' => 'client_credentials']);

        if (!$tokenResponse->ok()) {
            Log::error('M-Pesa token request failed', [
                'status' => $tokenResponse->status(),
                'body' => $tokenResponse->body(),
            ]);
            Toastr::error('Failed to get M-Pesa access token. Check credentials.', __('message.error'));
            return redirect()->back()->withInput($backInput);
        }

        $accessToken = $tokenResponse->json('access_token');
        if (!$accessToken) {
            Toastr::error('M-Pesa access token is missing.', __('message.error'));
            return redirect()->back()->withInput($backInput);
        }

        $timestamp = now()->format('YmdHis');
        $password = base64_encode($shortcode . ($passkey ?? '') . $timestamp);

        $stkPayload = [
            'BusinessShortCode' => $shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $phone,
            'PartyB' => $shortcode,
            'PhoneNumber' => $phone,
            'CallBackURL' => $callbackUrl,
            'AccountReference' => 'Marketplace Parcel',
            'TransactionDesc' => 'Marketplace parcel payment',
        ];

        Log::info('M-Pesa STK payload', [
            'environment' => $environment,
            'shortcode' => $shortcode,
            'timestamp' => $timestamp,
            'amount' => $amount,
            'party_a' => $phone,
            'party_b' => $shortcode,
            'callback_url' => $callbackUrl,
            'password_len' => strlen($password),
        ]);

        $stkResponse = Http::withToken($accessToken)
            ->timeout(20)
            ->connectTimeout(10)
            ->post($baseUrl . '/mpesa/stkpush/v1/processrequest', $stkPayload);

        if (!$stkResponse->ok()) {
            Log::error('M-Pesa STK push HTTP failed', [
                'status' => $stkResponse->status(),
                'body' => $stkResponse->body(),
            ]);
            Toastr::error('M-Pesa STK push failed (HTTP). Check credentials and callback URL.', __('message.error'));
            return redirect()->back()->withInput($backInput);
        }

        $responseBody = $stkResponse->json();
        if (($responseBody['ResponseCode'] ?? null) !== '0') {
            Log::error('M-Pesa STK push error response', ['body' => $responseBody]);
            Toastr::error($responseBody['ResponseDescription'] ?? 'M-Pesa payment failed.', __('message.error'));
            return redirect()->back()->withInput($backInput);
        }

        MpesaPayment::create([
            'merchant_id' => optional($request->user())->id,
            'phone' => $phone,
            'amount' => $amount,
            'status' => 'pending',
            'parcel_payload' => $parcelPayload,
            'checkout_request_id' => $responseBody['CheckoutRequestID'] ?? null,
            'merchant_request_id' => $responseBody['MerchantRequestID'] ?? null,
            'mpesa_response' => $responseBody,
        ]);

        Toastr::success('M-Pesa prompt sent to the sender phone.', __('message.success'));
        return redirect()->back()
            ->with('mpesa_prompt_sent', true)
            ->withInput(array_merge($backInput, [
                'mpesa_checkout_request_id' => $responseBody['CheckoutRequestID'] ?? null,
            ]));
    }

    public function callback(Request $request)
    {
        $data = $request->all();
        $stk = $data['Body']['stkCallback'] ?? [];
        $checkoutId = $stk['CheckoutRequestID'] ?? null;
        $resultCode = $stk['ResultCode'] ?? null;

        if (!$checkoutId) {
            Log::warning('M-Pesa callback missing CheckoutRequestID', $data);
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Invalid callback']);
        }

        $payment = MpesaPayment::where('checkout_request_id', $checkoutId)->first();
        if (!$payment) {
            Log::warning('M-Pesa callback: payment not found', ['checkout_request_id' => $checkoutId]);
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Payment not found']);
        }

        $payment->callback_payload = $data;
        if ((int) $resultCode === 0) {
            $payment->status = 'success';
        } else {
            $payment->status = 'failed';
        }
        $payment->save();

        if ($payment->status === 'success' && $payment->parcel_id) {
            $parcel = Parcel::find($payment->parcel_id);
            if ($parcel) {
                $parcel->payment_status = InvoiceStatus::PAID;
                if (in_array((int) $parcel->status, [
                    ParcelStatus::MARKETPLACE_SENDER_PAYMENT_AWAITING,
                    ParcelStatus::MARKETPLACE_RECEIVER_PAYMENT_AWAITING,
                ], true)) {
                    $parcel->status = ParcelStatus::MARKETPLACE_PENDING;
                }
                $parcel->save();
            }
        }

        if ($payment->status === 'failed' && $payment->parcel_id) {
            $parcel = Parcel::find($payment->parcel_id);
            if ($parcel) {
                $parcel->payment_status = InvoiceStatus::UNPAID;
                if ((int) ($parcel->who_pays_either ?? 0) === WhoPays::SENDER) {
                    $parcel->status = ParcelStatus::MARKETPLACE_SENDER_PAYMENT_AWAITING;
                } elseif ((int) ($parcel->who_pays_either ?? 0) === WhoPays::RECIPIENT) {
                    $parcel->status = ParcelStatus::MARKETPLACE_RECEIVER_PAYMENT_AWAITING;
                } else {
                    // Third-party follows sender-flow awaiting behavior.
                    $parcel->status = ParcelStatus::MARKETPLACE_SENDER_PAYMENT_AWAITING;
                }
                $parcel->save();
            }
        }

        $shouldCreateOnCallback = (bool) (($payment->parcel_payload['create_on_callback'] ?? true));
        if ($payment->status === 'success' && !$payment->parcel_id && $shouldCreateOnCallback) {
            $parcelPayload = $payment->parcel_payload ?? [];
            $parcelRequest = new Request($parcelPayload);
            $parcelRequest->merge(['payment_confirmed' => true]);
            if (($parcelRequest->from_state_id ?? null) == ($parcelRequest->to_state_id ?? null)) {
                $parcelRequest->merge(['delivery_type_id' => \App\Enums\DeliveryType::SAMEDAY]);
            } else {
                $parcelRequest->merge(['delivery_type_id' => \App\Enums\DeliveryType::SUBCITY]);
            }

            $merchant = $this->merchantRepo->getMerchant($payment->merchant_id);
            if ($merchant) {
                $created = $this->parcelRepo->store($parcelRequest, $merchant->id);
                if ($created) {
                    $latestParcel = \App\Models\Backend\Parcel::where('merchant_id', $merchant->id)->orderByDesc('id')->first();
                    if ($latestParcel) {
                        $payment->parcel_id = $latestParcel->id;
                        $payment->save();
                    }
                }
            }
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }

    public function payParcel(Request $request, Parcel $parcel)
    {
        if ($guard = $this->validatePayLaterParcelAccess($request, $parcel)) {
            return $guard;
        }

        $amount = (float) ($parcel->final_paid_amount ?? $parcel->total_delivery_amount ?? 0);
        if ($amount <= 0) {
            Toastr::error('Invalid parcel payable amount for M-Pesa.', __('message.error'));
            return redirect()->route('merchant-panel.mpesa.pay.parcel.form', $parcel->id)->withInput();
        }

        $request->validate([
            'who_pays_either' => 'required|in:' . WhoPays::SENDER . ',' . WhoPays::RECIPIENT,
            'phone' => 'required|string',
        ]);

        $whoPays = (int) $request->who_pays_either;
        $phone = (string) $request->phone;

        $parcel->who_pays_either = $whoPays;
        if ($whoPays === WhoPays::RECIPIENT) {
            $parcel->receiver_mpesa_phone = $phone;
        }
        $parcel->save();

        $payload = [
            'merchant_id' => optional($request->user())->id,
            'parcel_id' => $parcel->id,
            'phone' => $phone,
            'amount' => $amount,
            'account_reference' => $whoPays === WhoPays::RECIPIENT ? 'Receiver Payment' : 'Sender Payment',
            'transaction_desc' => ($whoPays === WhoPays::RECIPIENT ? 'Receiver' : 'Sender') . ' payment for parcel ' . $parcel->tracking_id,
            'parcel_payload' => [
                'parcel_id' => $parcel->id,
                'who_pays' => $whoPays === WhoPays::RECIPIENT ? 'receiver' : 'sender',
            ],
        ];

        $result = app(MpesaStkPushService::class)->initiate($payload);
        if (empty($result['success'])) {
            Toastr::error($result['message'] ?? 'Failed to send M-Pesa prompt.', __('message.error'));
            return redirect()->route('merchant-panel.mpesa.pay.parcel.form', $parcel->id)->withInput();
        }

        // Prompt accepted by M-Pesa API; move out of awaiting-payment status
        // and keep payment_status as processing until callback confirms result.
        $parcel->payment_status = InvoiceStatus::PROCESSING;
        if (in_array((int) $parcel->status, [
            ParcelStatus::MARKETPLACE_SENDER_PAYMENT_AWAITING,
            ParcelStatus::MARKETPLACE_RECEIVER_PAYMENT_AWAITING,
        ], true)) {
            $parcel->status = ParcelStatus::MARKETPLACE_PENDING;
        }
        $parcel->save();

        Toastr::success('M-Pesa prompt sent for this parcel.', __('message.success'));
        return redirect()->back();
    }

    public function payParcelForm(Request $request, Parcel $parcel)
    {
        if ($guard = $this->validatePayLaterParcelAccess($request, $parcel)) {
            return $guard;
        }

        $senderPhone = (string) ($parcel->sender_phone
            ?? $parcel->pickup_phone
            ?? optional($request->user())->mobile
            ?? '');
        $receiverPhone = (string) ($parcel->receiver_mpesa_phone
            ?? $parcel->customer_phone
            ?? '');
        $selectedWhoPays = (int) old('who_pays_either', (int) ($parcel->who_pays_either ?? WhoPays::SENDER));
        $defaultPhone = old('phone', $selectedWhoPays === WhoPays::RECIPIENT ? $receiverPhone : $senderPhone);

        return view('backend.merchant_panel.mpesa_payment_history.pay_parcel', [
            'parcel' => $parcel,
            'selectedWhoPays' => $selectedWhoPays,
            'senderPhone' => $senderPhone,
            'receiverPhone' => $receiverPhone,
            'defaultPhone' => $defaultPhone,
        ]);
    }

    private function validatePayLaterParcelAccess(Request $request, Parcel $parcel)
    {
        $merchantUserId = optional(optional($parcel->merchant)->user)->id;
        if ((int) $merchantUserId !== (int) optional($request->user())->id) {
            Toastr::error('You are not allowed to pay for this parcel.', __('message.error'));
            return redirect()->route('merchant-panel.parcel.index');
        }

        if (in_array((int) $parcel->status, [
            ParcelStatus::MARKETPLACE_DELIVERED,
            ParcelStatus::MARKETPLACE_CANCELLED,
        ], true)) {
            Toastr::error('Payment cannot be updated for delivered/cancelled parcels.', __('message.error'));
            return redirect()->route('merchant-panel.parcel.index');
        }

        if ((int) ($parcel->payment_status ?? 0) === InvoiceStatus::PAID) {
            Toastr::success('This parcel is already paid.', __('message.success'));
            return redirect()->route('merchant-panel.parcel.index');
        }

        return null;
    }

    private function normalizePhone(string $phone): ?string
    {
        $clean = preg_replace('/\D+/', '', $phone);
        if (!$clean) {
            return null;
        }

        // Accept 07XXXXXXXX
        if (str_starts_with($clean, '0') && strlen($clean) === 10) {
            return '254' . substr($clean, 1);
        }

        // Accept 7XXXXXXXX
        if (str_starts_with($clean, '7') && strlen($clean) === 9) {
            return '254' . $clean;
        }

        // Accept 2547XXXXXXXX
        if (str_starts_with($clean, '254') && strlen($clean) === 12) {
            return $clean;
        }

        return null;
    }
}
