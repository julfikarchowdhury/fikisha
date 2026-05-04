<?php

namespace App\Services;

use App\Models\Backend\Parcel;
use App\Models\Backend\PlatformLedgerTransaction;

class PlatformLedgerService
{
    public function recordDisputePlatformLiability(Parcel $parcel, float $refundAmount): void
    {
        $refundAmount = round($refundAmount, 2);
        if ($refundAmount <= 0) {
            return;
        }

        $commission = round((float) ($parcel->commission_amount ?? 0), 2);
        $markup = round((float) ($parcel->receiver_markup ?? 0), 2);

        $reverseCommission = min($commission, $refundAmount);
        $remaining = round($refundAmount - $reverseCommission, 2);
        $reverseMarkup = min($markup, $remaining);
        $remainingLoss = round($refundAmount - $reverseCommission - $reverseMarkup, 2);

        if ($reverseCommission > 0) {
            PlatformLedgerTransaction::create([
                'parcel_id' => $parcel->id,
                'type' => 'commission_reversal',
                'direction' => 'debit',
                'amount' => $reverseCommission,
                'description' => 'Dispute refund commission reversal for parcel #' . $parcel->id,
            ]);
        }

        if ($reverseMarkup > 0) {
            PlatformLedgerTransaction::create([
                'parcel_id' => $parcel->id,
                'type' => 'receiver_markup_reversal',
                'direction' => 'debit',
                'amount' => $reverseMarkup,
                'description' => 'Dispute refund receiver markup reversal for parcel #' . $parcel->id,
            ]);
        }

        if ($remainingLoss > 0) {
            PlatformLedgerTransaction::create([
                'parcel_id' => $parcel->id,
                'type' => 'dispute_platform_loss',
                'direction' => 'debit',
                'amount' => $remainingLoss,
                'description' => 'Dispute refund platform loss for parcel #' . $parcel->id,
            ]);
        }
    }
}
