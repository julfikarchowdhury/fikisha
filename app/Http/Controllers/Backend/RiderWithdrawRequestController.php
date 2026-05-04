<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Backend\RiderWithdrawRequest;
use App\Services\RiderWalletService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiderWithdrawRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = RiderWithdrawRequest::query()->with(['rider']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->rider_id) {
            $query->where('rider_id', $request->rider_id);
        }

        $requests = $query->orderByDesc('id')->paginate(20);

        return view('backend.rider_withdraw_requests.index', compact('requests', 'request'));
    }

    public function approve($id, RiderWalletService $service)
    {
        $withdraw = RiderWithdrawRequest::findOrFail($id);

        try {
            $service->approveWithdrawal($withdraw, (int) Auth::id());
            Toastr::success('Withdrawal approved and paid.');
        } catch (\RuntimeException $e) {
            Toastr::error($e->getMessage());
        }

        return redirect()->back();
    }

    public function reject(Request $request, $id, RiderWalletService $service)
    {
        $withdraw = RiderWithdrawRequest::findOrFail($id);

        try {
            $service->rejectWithdrawal($withdraw, (int) Auth::id(), $request->input('note'));
            Toastr::success('Withdrawal request rejected.');
        } catch (\RuntimeException $e) {
            Toastr::error($e->getMessage());
        }

        return redirect()->back();
    }
}
