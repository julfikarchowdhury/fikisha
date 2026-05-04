<?php

namespace App\Services;

use App\Models\Backend\MpesaPayment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaStkPushService
{
    public function initiate(array $payload): array
    {
        $consumerKey = globalSettings('mpesa_consumer_key');
        $consumerSecret = globalSettings('mpesa_consumer_secret');
        $shortcode = globalSettings('mpesa_shortcode');
        $passkey = globalSettings('mpesa_passkey');
        $callbackUrl = globalSettings('mpesa_callback_url');
        $environment = globalSettings('mpesa_environment') ?: 'sandbox';

        if (!$consumerKey || !$consumerSecret || !$shortcode || !$callbackUrl) {
            return [
                'success' => false,
                'message' => 'M-Pesa settings are incomplete.',
            ];
        }

        if ($environment === 'live' && !$passkey) {
            return [
                'success' => false,
                'message' => 'M-Pesa Passkey is required for production.',
            ];
        }

        $phone = $this->normalizePhone((string) ($payload['phone'] ?? ''));
        if (!$phone) {
            return [
                'success' => false,
                'message' => 'Invalid M-Pesa phone number.',
            ];
        }

        $amount = (int) ceil((float) ($payload['amount'] ?? 0));
        if ($amount <= 0) {
            return [
                'success' => false,
                'message' => 'Invalid payment amount.',
            ];
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
            return [
                'success' => false,
                'message' => 'Failed to get M-Pesa access token.',
            ];
        }

        $accessToken = $tokenResponse->json('access_token');
        if (!$accessToken) {
            return [
                'success' => false,
                'message' => 'M-Pesa access token is missing.',
            ];
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
            'AccountReference' => (string) ($payload['account_reference'] ?? 'Marketplace Parcel'),
            'TransactionDesc' => (string) ($payload['transaction_desc'] ?? 'Marketplace parcel payment'),
        ];

        $stkResponse = Http::withToken($accessToken)
            ->timeout(20)
            ->connectTimeout(10)
            ->post($baseUrl . '/mpesa/stkpush/v1/processrequest', $stkPayload);

        if (!$stkResponse->ok()) {
            Log::error('M-Pesa STK push HTTP failed', [
                'status' => $stkResponse->status(),
                'body' => $stkResponse->body(),
            ]);
            return [
                'success' => false,
                'message' => 'M-Pesa STK push failed (HTTP).',
            ];
        }

        $responseBody = $stkResponse->json();
        if (($responseBody['ResponseCode'] ?? null) !== '0') {
            Log::error('M-Pesa STK push error response', ['body' => $responseBody]);
            return [
                'success' => false,
                'message' => $responseBody['ResponseDescription'] ?? 'M-Pesa payment failed.',
            ];
        }

        Log::info('M-Pesa STK push success', [
            'phone' => $phone,
            'amount' => $amount,
            'checkout_request_id' => $responseBody['CheckoutRequestID'] ?? null,
            'merchant_request_id' => $responseBody['MerchantRequestID'] ?? null,
            'response' => $responseBody,
        ]);

        MpesaPayment::create([
            'merchant_id' => $payload['merchant_id'] ?? null,
            'parcel_id' => $payload['parcel_id'] ?? null,
            'phone' => $phone,
            'amount' => $amount,
            'status' => 'pending',
            'parcel_payload' => $payload['parcel_payload'] ?? null,
            'checkout_request_id' => $responseBody['CheckoutRequestID'] ?? null,
            'merchant_request_id' => $responseBody['MerchantRequestID'] ?? null,
            'mpesa_response' => $responseBody,
        ]);

        return [
            'success' => true,
            'message' => 'M-Pesa prompt sent.',
            'payload' => $responseBody,
        ];
    }

    private function normalizePhone(string $phone): ?string
    {
        $clean = preg_replace('/\D+/', '', $phone);
        if (!$clean) {
            return null;
        }

        if (str_starts_with($clean, '0') && strlen($clean) === 10) {
            return '254' . substr($clean, 1);
        }

        if (str_starts_with($clean, '7') && strlen($clean) === 9) {
            return '254' . $clean;
        }

        if (str_starts_with($clean, '254') && strlen($clean) === 12) {
            return $clean;
        }

        return null;
    }
}
