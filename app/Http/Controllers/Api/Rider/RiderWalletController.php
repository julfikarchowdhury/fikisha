<?php

namespace App\Http\Controllers\Api\Rider;

use App\Http\Controllers\Controller;
use App\Services\RiderWalletService;
use App\Models\Backend\RiderWalletTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RiderWalletController extends Controller
{
    public function summary(Request $request, RiderWalletService $service): JsonResponse
    {
        $riderId = (int) $request->user()->id;
        $summary = $service->getSummaryForRider($riderId);

        return response()->json([
            'success' => true,
            'balance' => $summary['balance'],
            'pending_withdraw_amount' => $summary['pending_withdraw_amount'],
            'available_balance' => $summary['available_balance'],
            'total_earned' => $summary['total_earned'],
            'total_withdrawn' => $summary['total_withdrawn'],
        ]);
    }

    public function requestWithdrawal(Request $request, RiderWalletService $service): JsonResponse
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'gt:0'],
        ]);

        $riderId = (int) $request->user()->id;
        $amount = (float) $request->input('amount');

        try {
            $withdraw = $service->requestWithdrawal($riderId, $amount);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Withdrawal request submitted.',
            'request_id' => $withdraw->id,
        ]);
    }

    public function listRequests(Request $request): JsonResponse
    {
        $riderId = (int) $request->user()->id;
        $perPage = (int) $request->query('per_page', 20);
        if ($perPage < 1) {
            $perPage = 20;
        }
        if ($perPage > 100) {
            $perPage = 100;
        }

        $requests = \App\Models\Backend\RiderWithdrawRequest::where('rider_id', $riderId)
            ->orderByDesc('id')
            ->paginate($perPage, [
                'id',
                'amount',
                'status',
                'requested_at',
                'approved_at',
                'note',
            ]);

        return response()->json([
            'success' => true,
            'data' => $requests->items(),
            'meta' => [
                'current_page' => $requests->currentPage(),
                'per_page' => $requests->perPage(),
                'total' => $requests->total(),
                'last_page' => $requests->lastPage(),
            ],
        ]);
    }

    public function transactions(Request $request): JsonResponse
    {
        $riderId = (int) $request->user()->id;
        $perPage = (int) $request->query('per_page', 20);
        if ($perPage < 1) {
            $perPage = 20;
        }
        if ($perPage > 100) {
            $perPage = 100;
        }

        $transactions = RiderWalletTransaction::where('rider_id', $riderId)
            ->orderByDesc('id')
            ->paginate($perPage, ['id', 'parcel_id', 'type', 'amount', 'description', 'created_at']);

        $data = $transactions->getCollection()->map(function ($tx) {
            $source = 'other';
            if ($tx->type === 'credit' && str_starts_with((string) $tx->description, 'Earning for parcel')) {
                $source = 'parcel_delivery';
            } elseif ($tx->type === 'debit' && str_contains((string) $tx->description, 'Dispute #')) {
                $source = 'dispute';
            } elseif ($tx->type === 'debit' && (str_contains((string) $tx->description, 'Withdrawal') || str_contains((string) $tx->description, 'payout'))) {
                $source = 'withdrawal';
            }

            return [
                'id' => $tx->id,
                'type' => $tx->type,
                'source' => $source,
                'amount' => (float) $tx->amount,
                'parcel_id' => $tx->parcel_id,
                'description' => $tx->description,
                'created_at' => $tx->created_at,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $data,
            'meta' => [
                'current_page' => $transactions->currentPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
                'last_page' => $transactions->lastPage(),
            ],
        ]);
    }

    public function earnings(Request $request): JsonResponse
    {
        $riderId = (int) $request->user()->id;
        $today = now()->toDateString();
        $startOfWeek = now()->startOfWeek()->toDateString();
        $startOfMonth = now()->startOfMonth()->toDateString();

        $baseQuery = RiderWalletTransaction::where('rider_id', $riderId)
            ->where('type', 'credit');

        $todayEarning = (float) (clone $baseQuery)
            ->whereDate('created_at', $today)
            ->sum('amount');

        $weeklyEarning = (float) (clone $baseQuery)
            ->whereDate('created_at', '>=', $startOfWeek)
            ->sum('amount');

        $monthlyEarning = (float) (clone $baseQuery)
            ->whereDate('created_at', '>=', $startOfMonth)
            ->sum('amount');

        $lifetimeEarning = (float) (clone $baseQuery)->sum('amount');

        return response()->json([
            'success' => true,
            'today_earning' => round($todayEarning, 2),
            'weekly_earning' => round($weeklyEarning, 2),
            'monthly_earning' => round($monthlyEarning, 2),
            'lifetime_earning' => round($lifetimeEarning, 2),
        ]);
    }
}
