<?php

namespace App\Http\Controllers\Backend;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\Backend\RiderWallet;
use App\Models\Backend\RiderWalletTransaction;
use App\Models\User;
use App\Services\RiderWalletService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RiderWalletController extends Controller
{
    protected RiderWalletService $walletService;

    public function __construct(RiderWalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function index(Request $request)
    {
        $riders = User::where('user_type', UserType::DELIVERYMAN)->orderBy('name')->get();
        $riderId = $request->filled('rider_id') ? (int) $request->rider_id : null;
        $fromDate = $request->from_date;
        $toDate = $request->to_date;

        $summary = [
            'balance' => (float) RiderWallet::sum('balance'),
            'pending_withdraw_amount' => (float) RiderWallet::sum('pending_withdraw_amount'),
            'available_balance' => (float) RiderWallet::sum('balance') - (float) RiderWallet::sum('pending_withdraw_amount'),
            'total_earned' => (float) RiderWalletTransaction::where('type', 'credit')->sum('amount'),
            'total_withdrawn' => (float) RiderWalletTransaction::where('type', 'debit')->sum('amount'),
        ];

        if ($riderId) {
            $summary = $this->walletService->getSummaryForRider($riderId);
        }

        $transactions = RiderWalletTransaction::query()
            ->leftJoin('users', 'users.id', '=', 'rider_wallet_transactions.rider_id')
            ->select('rider_wallet_transactions.*', 'users.name as rider_name')
            ->when($riderId, function ($query) use ($riderId) {
                $query->where('rider_wallet_transactions.rider_id', $riderId);
            })
            ->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('rider_wallet_transactions.created_at', [$fromDate, $toDate]);
            })
            ->orderByDesc('rider_wallet_transactions.id')
            ->paginate(20);

        return view('backend.rider_wallets.index', compact('riders', 'summary', 'transactions', 'request'));
    }

    public function adjust(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rider_id' => 'required|exists:users,id',
            'type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            Toastr::error($validator->errors()->first(), __('message.error'));
            return redirect()->back()->withInput();
        }

        $riderId = (int) $request->rider_id;
        $amount = (float) $request->amount;
        $note = $request->note ? trim((string) $request->note) : null;

        try {
            if ($request->type === 'credit') {
                $this->walletService->manualCredit($riderId, $amount, $note);
            } else {
                $this->walletService->manualDebit($riderId, $amount, $note);
            }
        } catch (\Throwable $e) {
            Toastr::error($e->getMessage(), __('message.error'));
            return redirect()->back()->withInput();
        }

        Toastr::success('Wallet updated successfully.', __('message.success'));
        return redirect()->back();
    }
}
