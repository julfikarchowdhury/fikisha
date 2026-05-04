<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Backend\DeliveryMan;
use App\Models\Backend\Parcel;
use App\Models\Backend\ParcelDispute;
use App\Models\Backend\ParcelEvent;
use App\Models\Backend\RiderLocation;
use App\Models\Backend\RiderWallet;
use App\Models\Backend\Upload;
use App\Models\Backend\RiderWalletTransaction;
use App\Services\RiderWalletService;
use App\Enums\InvoiceStatus;
use App\Enums\WhoPays;
use App\Services\PlatformLedgerService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParcelDisputeController extends Controller
{
    public function index(Request $request)
    {
        $query = ParcelDispute::query()->with('parcel');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('raised_by')) {
            $query->where('raised_by', $request->raised_by);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $disputes = $query->orderByDesc('id')->paginate(20)->appends($request->query());

        return view('backend.parcel_disputes.index', compact('disputes', 'request'));
    }

    public function show($id)
    {
        $dispute = ParcelDispute::with('parcel')->findOrFail($id);
        $parcel = $dispute->parcel;
        $rider = null;
        if ($parcel && $parcel->delivery_man_id) {
            $rider = DeliveryMan::with('user')
                ->where('user_id', $parcel->delivery_man_id)
                ->first();
        }

        $evidenceIds = $dispute->evidence_files ?? [];
        $evidenceUploads = [];
        if (!empty($evidenceIds)) {
            $evidenceUploads = Upload::whereIn('id', $evidenceIds)->get();
        }

        $timeline = [];
        if ($parcel) {
            $timeline = ParcelEvent::with(['deliveryMan', 'pickupman', 'user'])
                ->where('parcel_id', $parcel->id)
                ->orderBy('created_at', 'desc')
                ->get();
            if (!$rider) {
                $latestRiderEvent = $timeline->first(function ($event) {
                    return !empty($event->delivery_man_id);
                });
                if ($latestRiderEvent && $latestRiderEvent->delivery_man_id) {
                    $rider = DeliveryMan::with('user')->find($latestRiderEvent->delivery_man_id);
                }
            }
        }

        $wallet = null;
        $riderDisputeCount = 0;
        $riderDisputeLossCount = 0;
        $lastLocation = null;
        if ($parcel && ($parcel->delivery_man_id || $rider)) {
            $riderUserId = $parcel->delivery_man_id ?: ($rider?->user_id);
            $wallet = $riderUserId ? RiderWallet::where('rider_id', $riderUserId)->first() : null;
            $riderDisputeCount = ParcelDispute::whereHas('parcel', function ($query) use ($parcel) {
                $query->where('delivery_man_id', $parcel->delivery_man_id);
            })->count();
            $riderDisputeLossCount = ParcelDispute::whereHas('parcel', function ($query) use ($parcel) {
                $query->where('delivery_man_id', $parcel->delivery_man_id);
            })->where('liability', 'rider')->where('status', 'resolved')->count();
            $lastLocation = $riderUserId ? RiderLocation::where('rider_id', $riderUserId)
                ->orderByDesc('id')
                ->first() : null;
        }

        $parcelDisputeCount = ParcelDispute::where('parcel_id', $dispute->parcel_id)->count();

        return view('backend.parcel_disputes.show', compact(
            'dispute',
            'parcel',
            'rider',
            'evidenceUploads',
            'timeline',
            'wallet',
            'riderDisputeCount',
            'riderDisputeLossCount',
            'lastLocation',
            'parcelDisputeCount'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,under_review,resolved,rejected',
            'admin_decision' => 'nullable|string',
            'liability' => 'nullable|in:rider,sender,platform',
            'refund_amount' => 'nullable|numeric|min:0',
            'rider_liability_amount' => 'nullable|numeric|min:0',
            'refund_method' => 'nullable|string',
            'refund_reference_id' => 'nullable|string',
            'refund_processed_at' => 'nullable|date',
            'refund_status' => 'nullable|in:pending,processed',
            'refund_note' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $dispute = ParcelDispute::whereKey($id)->lockForUpdate()->firstOrFail();
                $previousStatus = $dispute->status;

                if ($request->liability === 'rider') {
                    $refundAmount = (float) ($request->refund_amount ?? 0);
                    if ($refundAmount <= 0) {
                        throw new \RuntimeException('Refund amount must be greater than zero for rider liability.');
                    }
                }

                if ($request->liability === 'platform') {
                    $refundAmount = (float) ($request->refund_amount ?? 0);
                    if ($refundAmount <= 0) {
                        throw new \RuntimeException('Refund amount must be greater than zero for platform liability.');
                    }
                }

                $dispute->status = $request->status;
            $dispute->admin_decision = $request->admin_decision;
            $dispute->liability = $request->liability;
            $dispute->refund_amount = $request->refund_amount;
            $dispute->rider_liability_amount = $request->rider_liability_amount;
                $dispute->refund_method = $request->refund_method;
                $dispute->refund_reference_id = $request->refund_reference_id;
                $dispute->refund_processed_at = $request->refund_processed_at;
                $dispute->refund_status = $request->refund_status ?? $dispute->refund_status;
                $dispute->refund_note = $request->refund_note;

                if ($dispute->refund_status === 'processed' && !$dispute->refund_processed_by) {
                    $dispute->refund_processed_by = auth()->id();
                }

            if (in_array($request->status, ['resolved', 'rejected'], true) && !$dispute->resolved_at) {
                $dispute->resolved_at = now();
            }

            $dispute->save();

                if ($previousStatus !== 'resolved' && $request->status === 'resolved') {
                    $refundAmount = round((float) ($dispute->refund_amount ?? 0), 2);
                    if ($refundAmount > 0) {
                        $parcel = Parcel::find($dispute->parcel_id);
                        if ($parcel) {
                            $paymentStatus = (int) ($parcel->payment_status ?? 0);
                            if ($paymentStatus !== InvoiceStatus::PAID) {
                                throw new \RuntimeException('Cannot process refund while payment is unpaid.');
                            }

                            $alreadyProcessed = RiderWalletTransaction::where('parcel_id', $parcel->id)
                            ->where('description', 'like', 'Dispute #' . $dispute->id . '%')
                            ->exists();

                        if (!$alreadyProcessed) {
                            $riderId = (int) ($parcel->delivery_man_id ?? 0);
                            if ($riderId <= 0) {
                                $latestEvent = ParcelEvent::where('parcel_id', $parcel->id)
                                    ->whereNotNull('delivery_man_id')
                                    ->orderByDesc('id')
                                    ->first();
                                $deliveryManId = $latestEvent?->delivery_man_id;
                                if ($deliveryManId) {
                                    $deliveryMan = DeliveryMan::find($deliveryManId);
                                    $riderId = (int) ($deliveryMan->user_id ?? 0);
                                }
                            }
                            $liability = $dispute->liability;
                            $riderDebitAmount = 0.0;

                            if ($liability === 'rider') {
                                $riderDebitAmount = $refundAmount;
                            }

                            if ($riderDebitAmount > 0) {
                                if ($riderId <= 0) {
                                    throw new \RuntimeException('Rider not found for this parcel.');
                                }

                                app(RiderWalletService::class)->debitForDispute(
                                    $riderId,
                                    $riderDebitAmount,
                                    $parcel->id,
                                    'Dispute #' . $dispute->id . ' liability debit'
                                );
                            }

                            if ($liability === 'platform') {
                                app(PlatformLedgerService::class)->recordDisputePlatformLiability($parcel, $refundAmount);
                            }
                        }
                    }
                    }
                }
            });
        } catch (\RuntimeException $e) {
            Toastr::error($e->getMessage(), __('message.error'));
            return redirect()->back();
        }

        Toastr::success('Dispute updated successfully.', __('message.success'));
        return redirect()->back();
    }
}
