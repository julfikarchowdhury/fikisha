<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Backend\PlatformLedgerTransaction;
use Illuminate\Http\Request;

class PlatformLedgerController extends Controller
{
    public function index(Request $request)
    {
        $query = PlatformLedgerTransaction::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('direction')) {
            $query->where('direction', $request->direction);
        }
        if ($request->filled('parcel_id')) {
            $query->where('parcel_id', $request->parcel_id);
        }
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->orderByDesc('id')->paginate(20)->appends($request->query());

        $credits = (clone $query)->where('direction', 'credit')->sum('amount');
        $debits = (clone $query)->where('direction', 'debit')->sum('amount');
        $net = round(((float) $credits - (float) $debits), 2);

        return view('backend.platform_ledger.index', compact('transactions', 'request', 'credits', 'debits', 'net'));
    }
}
