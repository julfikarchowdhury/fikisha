<?php

namespace App\Services;

use App\Enums\ParcelStatus;
use App\Models\Backend\Parcel;
use App\Models\Backend\RiderWallet;
use App\Models\Backend\RiderWalletTransaction;
use App\Models\Backend\RiderWithdrawRequest;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class RiderWalletService
{
    public const DEFAULT_MIN_WITHDRAWAL = 500.00;

    public function manualCredit(int $riderId, float $amount, ?string $note = null): void
    {
        $amount = round($amount, 2);
        if ($amount <= 0) {
            throw new RuntimeException('Credit amount must be greater than zero.');
        }

        DB::transaction(function () use ($riderId, $amount, $note) {
            $wallet = RiderWallet::firstOrCreate(
                ['rider_id' => $riderId],
                ['balance' => 0, 'pending_withdraw_amount' => 0]
            );

            $wallet = RiderWallet::where('rider_id', $riderId)->lockForUpdate()->first();
            if (!$wallet) {
                throw new RuntimeException('Rider wallet not found.');
            }

            $wallet->balance = round(((float) $wallet->balance) + $amount, 2);
            $wallet->save();

            RiderWalletTransaction::create([
                'rider_id' => $riderId,
                'type' => 'credit',
                'amount' => $amount,
                'description' => $note ?: 'Manual credit',
            ]);
        });
    }

    public function manualDebit(int $riderId, float $amount, ?string $note = null): void
    {
        $amount = round($amount, 2);
        if ($amount <= 0) {
            throw new RuntimeException('Debit amount must be greater than zero.');
        }

        DB::transaction(function () use ($riderId, $amount, $note) {
            $wallet = RiderWallet::where('rider_id', $riderId)->lockForUpdate()->first();
            if (!$wallet) {
                throw new RuntimeException('Rider wallet not found.');
            }

            $balance = round((float) $wallet->balance, 2);
            $pending = round((float) $wallet->pending_withdraw_amount, 2);
            $available = round($balance - $pending, 2);

            if ($available < $amount) {
                throw new RuntimeException('Insufficient available balance.');
            }

            $wallet->balance = round($balance - $amount, 2);
            $wallet->save();

            RiderWalletTransaction::create([
                'rider_id' => $riderId,
                'type' => 'debit',
                'amount' => $amount,
                'description' => $note ?: 'Manual debit',
            ]);
        });
    }

    public function debitForDispute(int $riderId, float $amount, int $parcelId, string $note): void
    {
        $amount = round($amount, 2);
        if ($amount <= 0) {
            throw new RuntimeException('Debit amount must be greater than zero.');
        }

        DB::transaction(function () use ($riderId, $amount, $parcelId, $note) {
            $wallet = RiderWallet::firstOrCreate(
                ['rider_id' => $riderId],
                ['balance' => 0, 'pending_withdraw_amount' => 0]
            );

            $wallet = RiderWallet::where('rider_id', $riderId)->lockForUpdate()->first();
            if (!$wallet) {
                throw new RuntimeException('Rider wallet not found.');
            }

            $wallet->balance = round(((float) $wallet->balance) - $amount, 2);
            $wallet->save();

            RiderWalletTransaction::create([
                'rider_id' => $riderId,
                'parcel_id' => $parcelId,
                'type' => 'debit',
                'amount' => $amount,
                'description' => $note,
            ]);
        });
    }

    public function creditForParcel(Parcel $parcel): void
    {
        if ((int) $parcel->status !== ParcelStatus::MARKETPLACE_DELIVERED) {
            return;
        }

        if ($parcel->commission_amount === null) {
            return;
        }

        $riderId = (int) ($parcel->delivery_man_id ?? 0);
        if ($riderId <= 0) {
            return;
        }

        if ((int) $parcel->delivery_man_id !== $riderId) {
            return;
        }

        $alreadyCredited = RiderWalletTransaction::where('parcel_id', $parcel->id)
            ->where('type', 'credit')
            ->exists();

        if ($alreadyCredited) {
            return;
        }

        RiderWallet::firstOrCreate(
            ['rider_id' => $riderId],
            ['balance' => 0]
        );

        $wallet = RiderWallet::where('rider_id', $riderId)
            ->lockForUpdate()
            ->first();

        if (!$wallet) {
            return;
        }

        $amount = round((float) ($parcel->rider_earning ?? 0), 2);
        if ($amount <= 0) {
            return;
        }

        $wallet->balance = round(((float) $wallet->balance) + $amount, 2);
        $wallet->save();

        RiderWalletTransaction::create([
            'rider_id' => $riderId,
            'parcel_id' => $parcel->id,
            'type' => 'credit',
            'amount' => $amount,
            'description' => 'Earning for parcel #' . $parcel->id,
        ]);
    }

    public function debitForPayout(int $riderId, float $amount): void
    {
        $amount = round($amount, 2);
        if ($amount <= 0) {
            throw new RuntimeException('Payout amount must be greater than zero.');
        }

        DB::transaction(function () use ($riderId, $amount) {
            $wallet = RiderWallet::where('rider_id', $riderId)->lockForUpdate()->first();
            if (!$wallet) {
                throw new RuntimeException('Rider wallet not found.');
            }

            $currentBalance = round((float) $wallet->balance, 2);
            if ($currentBalance < $amount) {
                throw new RuntimeException('Insufficient wallet balance.');
            }

            $wallet->balance = round($currentBalance - $amount, 2);
            $wallet->save();

            RiderWalletTransaction::create([
                'rider_id' => $riderId,
                'type' => 'debit',
                'amount' => $amount,
                'description' => 'Manual payout',
            ]);
        });
    }

    public function requestWithdrawal(int $riderId, float $amount, ?float $minLimit = null): RiderWithdrawRequest
    {
        $minLimit = $minLimit ?? (float) (settings()->rider_min_withdrawal_amount ?? self::DEFAULT_MIN_WITHDRAWAL);
        $amount = round($amount, 2);
        if ($amount < $minLimit) {
            throw new RuntimeException(
                'Requested amount is below the minimum withdrawal limit. Minimum amount is ' . number_format($minLimit, 2) . '.'
            );
        }

        return DB::transaction(function () use ($riderId, $amount) {
            $wallet = RiderWallet::where('rider_id', $riderId)->lockForUpdate()->first();
            if (!$wallet) {
                $wallet = RiderWallet::create([
                    'rider_id' => $riderId,
                    'balance' => 0,
                    'pending_withdraw_amount' => 0,
                ]);
            }

            $balance = round((float) $wallet->balance, 2);
            $pending = round((float) $wallet->pending_withdraw_amount, 2);
            $available = round($balance - $pending, 2);

            if ($available < $amount) {
                throw new RuntimeException('Insufficient available balance.');
            }

            $wallet->pending_withdraw_amount = round($pending + $amount, 2);
            $wallet->save();

            return RiderWithdrawRequest::create([
                'rider_id' => $riderId,
                'amount' => $amount,
                'status' => 'pending',
                'requested_at' => now(),
            ]);
        });
    }

    public function approveWithdrawal(RiderWithdrawRequest $request, int $adminId): void
    {
        DB::transaction(function () use ($request, $adminId) {
            $withdraw = RiderWithdrawRequest::whereKey($request->id)->lockForUpdate()->first();
            if (!$withdraw || $withdraw->status !== 'pending') {
                throw new RuntimeException('Withdraw request not available.');
            }

            $wallet = RiderWallet::where('rider_id', $withdraw->rider_id)->lockForUpdate()->first();
            if (!$wallet) {
                throw new RuntimeException('Rider wallet not found.');
            }

            $amount = round((float) $withdraw->amount, 2);
            $balance = round((float) $wallet->balance, 2);
            $pending = round((float) $wallet->pending_withdraw_amount, 2);

            if ($pending < $amount) {
                throw new RuntimeException('Pending amount is inconsistent.');
            }

            if ($balance < $amount) {
                throw new RuntimeException('Insufficient wallet balance.');
            }

            $wallet->balance = round($balance - $amount, 2);
            $wallet->pending_withdraw_amount = round($pending - $amount, 2);
            $wallet->save();

            RiderWalletTransaction::create([
                'rider_id' => $withdraw->rider_id,
                'type' => 'debit',
                'amount' => $amount,
                'description' => 'Withdrawal payout',
            ]);

            $withdraw->status = 'paid';
            $withdraw->approved_at = now();
            $withdraw->processed_by = $adminId;
            $withdraw->save();
        });
    }

    public function rejectWithdrawal(RiderWithdrawRequest $request, int $adminId, ?string $note = null): void
    {
        DB::transaction(function () use ($request, $adminId, $note) {
            $withdraw = RiderWithdrawRequest::whereKey($request->id)->lockForUpdate()->first();
            if (!$withdraw || $withdraw->status !== 'pending') {
                throw new RuntimeException('Withdraw request not available.');
            }

            $wallet = RiderWallet::where('rider_id', $withdraw->rider_id)->lockForUpdate()->first();
            if (!$wallet) {
                throw new RuntimeException('Rider wallet not found.');
            }

            $amount = round((float) $withdraw->amount, 2);
            $pending = round((float) $wallet->pending_withdraw_amount, 2);
            if ($pending < $amount) {
                throw new RuntimeException('Pending amount is inconsistent.');
            }

            $wallet->pending_withdraw_amount = round($pending - $amount, 2);
            $wallet->save();

            $withdraw->status = 'rejected';
            $withdraw->approved_at = now();
            $withdraw->processed_by = $adminId;
            if ($note !== null) {
                $withdraw->note = $note;
            }
            $withdraw->save();
        });
    }

    public function getSummaryForRider(int $riderId): array
    {
        $wallet = RiderWallet::where('rider_id', $riderId)->first();
        $balance = $wallet ? (float) $wallet->balance : 0.0;
        $pending = $wallet ? (float) $wallet->pending_withdraw_amount : 0.0;

        $totalEarned = (float) RiderWalletTransaction::where('rider_id', $riderId)
            ->where('type', 'credit')
            ->sum('amount');

        $totalWithdrawn = (float) RiderWalletTransaction::where('rider_id', $riderId)
            ->where('type', 'debit')
            ->sum('amount');

        return [
            'balance' => round($balance, 2),
            'pending_withdraw_amount' => round($pending, 2),
            'available_balance' => round($balance - $pending, 2),
            'total_earned' => round($totalEarned, 2),
            'total_withdrawn' => round($totalWithdrawn, 2),
        ];
    }
}
