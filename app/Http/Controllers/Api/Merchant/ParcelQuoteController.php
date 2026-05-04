<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Enums\UserType;
use App\Enums\WhoPays;
use App\Helpers\DeliveryChargeHelper;
use App\Http\Controllers\Controller;
use App\Models\Backend\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ParcelQuoteController extends Controller
{
    public function quote(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user || (int) $user->user_type !== UserType::MERCHANT) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized user type for merchant quote API.',
            ], 403);
        }

        $validated = $request->validate([
            'from_state_id' => ['required', 'integer', 'exists:provinces,id'],
            'to_state_id' => ['required', 'integer', 'exists:provinces,id'],
            'from_city_id' => ['required', 'integer', 'exists:cities,id'],
            'to_city_id' => ['required', 'integer', 'exists:cities,id'],
            'distance_km' => ['required', 'numeric', 'min:0'],
            'weight' => ['required', 'numeric', 'min:0'],
            'who_pays_either' => ['required', 'integer', 'in:' . WhoPays::SENDER . ',' . WhoPays::RECIPIENT . ',' . WhoPays::THIRD_PARTY],
            'payment_intent' => ['nullable', 'string', 'in:pay_now,pay_later'],
        ]);

        $fromCityBelongs = City::query()
            ->where('id', (int) $validated['from_city_id'])
            ->where('province_id', (int) $validated['from_state_id'])
            ->exists();
        if (!$fromCityBelongs) {
            return response()->json([
                'success' => false,
                'message' => 'From city does not belong to selected from state.',
            ], 422);
        }

        $toCityBelongs = City::query()
            ->where('id', (int) $validated['to_city_id'])
            ->where('province_id', (int) $validated['to_state_id'])
            ->exists();
        if (!$toCityBelongs) {
            return response()->json([
                'success' => false,
                'message' => 'To city does not belong to selected to state.',
            ], 422);
        }

        $whoPays = (int) $validated['who_pays_either'];
        $paymentIntent = (string) ($validated['payment_intent'] ?? 'pay_now');

        $warnings = [];
        $payLaterAllowed = $whoPays !== WhoPays::THIRD_PARTY;
        if (!$payLaterAllowed && $paymentIntent === 'pay_later') {
            $paymentIntent = 'pay_now';
            $warnings[] = 'Pay later is not allowed for third person payer. Switched to pay_now.';
        }

        $whoPaysForFlow = $whoPays === WhoPays::RECIPIENT ? WhoPays::RECIPIENT : WhoPays::SENDER;
        $breakdown = DeliveryChargeHelper::instance()->marketplacePricingBreakdown(
            (float) $validated['distance_km'],
            (float) $validated['weight'],
            $whoPaysForFlow
        );
        $hasPricingConfig = ((float) ($breakdown['base_fare'] ?? 0) > 0)
            || ((float) ($breakdown['per_km_rate'] ?? 0) > 0)
            || ((float) ($breakdown['per_kg_rate'] ?? 0) > 0);
        if (!$hasPricingConfig) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery charge is not configured for this route. Please configure pricing in admin panel.',
                'zone' => (string) ($breakdown['zone'] ?? ''),
            ], 422);
        }
        if ((float) ($breakdown['final'] ?? 0) <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Calculated delivery charge is zero. Please verify pricing, distance, and weight.',
                'zone' => (string) ($breakdown['zone'] ?? ''),
            ], 422);
        }

        $paymentRequiredNow = $whoPaysForFlow === WhoPays::SENDER && $paymentIntent === 'pay_now';
        $canCreateNow = !$paymentRequiredNow;
        $nextAction = $paymentRequiredNow ? 'send_mpesa_prompt' : 'create_parcel';

        return response()->json([
            'success' => true,
            'data' => [
                'route' => [
                    'from_state_id' => (int) $validated['from_state_id'],
                    'to_state_id' => (int) $validated['to_state_id'],
                    'from_city_id' => (int) $validated['from_city_id'],
                    'to_city_id' => (int) $validated['to_city_id'],
                    'distance_km' => round((float) $validated['distance_km'], 2),
                    'weight' => round((float) $validated['weight'], 2),
                ],
                'pricing' => [
                    'base_amount' => (float) $breakdown['base'],
                    'receiver_markup_amount' => (float) $breakdown['markup'],
                    'final_amount' => (float) $breakdown['final'],
                    'currency' => (string) (settings()->currency ?? ''),
                    'breakdown' => [
                        'zone' => (string) ($breakdown['zone'] ?? ''),
                        'distance_km' => (float) ($breakdown['distance_km'] ?? 0),
                        'weight_kg' => (float) ($breakdown['weight_kg'] ?? 0),
                        'base_fare' => (float) ($breakdown['base_fare'] ?? 0),
                        'per_km_rate' => (float) ($breakdown['per_km_rate'] ?? 0),
                        'per_kg_rate' => (float) ($breakdown['per_kg_rate'] ?? 0),
                        'distance_charge' => (float) ($breakdown['distance_charge'] ?? 0),
                        'weight_charge' => (float) ($breakdown['weight_charge'] ?? 0),
                        'receiver_markup_percent' => (float) ($breakdown['receiver_markup_percent'] ?? 0),
                    ],
                ],
                'payment' => [
                    'who_pays_either' => $whoPays,
                    'payment_intent' => $paymentIntent,
                    'pay_later_allowed' => $payLaterAllowed,
                    'payment_required_now' => $paymentRequiredNow,
                    'can_create_now' => $canCreateNow,
                    'next_action' => $nextAction,
                ],
                'warnings' => $warnings,
            ],
        ]);
    }
}

