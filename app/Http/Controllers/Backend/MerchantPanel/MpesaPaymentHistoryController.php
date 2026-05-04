<?php

namespace App\Http\Controllers\Backend\MerchantPanel;

use App\Http\Controllers\Controller;
use App\Models\Backend\MpesaPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MpesaPaymentHistoryController extends Controller
{
    public function index(Request $request)
    {
        $merchantId = auth()->id();

        $paymentsQuery = MpesaPayment::with(['parcel', 'parcel.merchant'])
            ->where(function ($query) use ($merchantId) {
                $query->where('merchant_id', $merchantId)
                    ->orWhereHas('parcel', function ($parcelQuery) use ($merchantId) {
                        $parcelQuery->whereHas('merchant', function ($merchantQuery) use ($merchantId) {
                            $merchantQuery->where('user_id', $merchantId);
                        });
                    });
            })
            ->orderByDesc('id');

        if (!empty($request->date)) {
            $date = explode('To', $request->date);
            if (count($date) === 2) {
                $from = Carbon::parse(trim($date[0]))->startOfDay()->toDateTimeString();
                $to = Carbon::parse(trim($date[1]))->endOfDay()->toDateTimeString();
                $paymentsQuery->whereBetween('created_at', [$from, $to]);
            }
        }

        if (!empty($request->status)) {
            $paymentsQuery->where('status', $request->status);
        }

        if (!empty($request->payer)) {
            $paymentsQuery->where(function ($query) use ($request) {
                if ($request->payer === 'receiver') {
                    $query->where('parcel_payload->who_pays', 'receiver')
                        ->orWhere('parcel_payload->who_pays', \App\Enums\WhoPays::RECIPIENT)
                        ->orWhereHas('parcel', function ($parcelQuery) {
                            $parcelQuery->where('who_pays', \App\Enums\WhoPays::RECIPIENT);
                        });
                } else {
                    $query->where('parcel_payload->who_pays', 'sender')
                        ->orWhere('parcel_payload->who_pays', \App\Enums\WhoPays::SENDER)
                        ->orWhereHas('parcel', function ($parcelQuery) {
                            $parcelQuery->where('who_pays', \App\Enums\WhoPays::SENDER);
                        })
                        ->orWhereNull('parcel_id');
                }
            });
        }

        if (!empty($request->search)) {
            $search = trim($request->search);
            $paymentsQuery->where(function ($query) use ($search) {
                $query->where('checkout_request_id', 'like', '%' . $search . '%')
                    ->orWhere('merchant_request_id', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhereHas('parcel', function ($parcelQuery) use ($search) {
                        $parcelQuery->where('tracking_id', 'like', '%' . $search . '%');
                    });
            });
        }

        $payments = $paymentsQuery->paginate(15)->appends($request->all());

        return view('backend.merchant_panel.mpesa_payment_history.index', compact('payments', 'request'));
    }
}
