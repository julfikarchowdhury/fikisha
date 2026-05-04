@extends('backend.partials.master')
@section('title')
    Rider Wallets
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="breadcrumb-link">{{ __('levels.dashboard') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Rider Wallets</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('rider.wallets.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="from_date" class="d-block">From</label>
                                <input id="from_date" type="date" name="from_date" class="form-control" value="{{ $request->from_date }}">
                            </div>
                            <div class="col-md-3">
                                <label for="to_date" class="d-block">To</label>
                                <input id="to_date" type="date" name="to_date" class="form-control" value="{{ $request->to_date }}">
                            </div>
                            <div class="col-md-4">
                                <label for="rider_id" class="d-block">Rider</label>
                                <select id="rider_id" name="rider_id" class="form-control select2">
                                    <option value="">All Riders</option>
                                    @foreach ($riders as $rider)
                                        <option value="{{ $rider->id }}" @selected($request->rider_id == $rider->id)>{{ $rider->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100" style="height: 39px; margin: 0px !important;">{{ __('levels.filter') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-2" style="font-size: 14px; font-weight: 500;">Balance</h6>
                    <h4 class="mb-0" style="font-size: 20px; font-weight: 500;">{{ settings()->currency }} {{ number_format((float) ($summary['balance'] ?? 0), 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-2" style="font-size: 14px; font-weight: 500;">Pending Withdrawals</h6>
                    <h4 class="mb-0" style="font-size: 20px; font-weight: 500;">{{ settings()->currency }} {{ number_format((float) ($summary['pending_withdraw_amount'] ?? 0), 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-2" style="font-size: 14px; font-weight: 500;">Available</h6>
                    <h4 class="mb-0" style="font-size: 20px; font-weight: 500;">{{ settings()->currency }} {{ number_format((float) ($summary['available_balance'] ?? 0), 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-2" style="font-size: 14px; font-weight: 500;">Total Earned</h6>
                    <h4 class="mb-0" style="font-size: 20px; font-weight: 500;">{{ settings()->currency }} {{ number_format((float) ($summary['total_earned'] ?? 0), 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-2" style="font-size: 14px; font-weight: 500;">Total Withdrawn</h6>
                    <h4 class="mb-0" style="font-size: 20px; font-weight: 500;">{{ settings()->currency }} {{ number_format((float) ($summary['total_withdrawn'] ?? 0), 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Manual Adjustment</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('rider.wallets.adjust') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="adj_rider_id" class="d-block">Rider</label>
                                <select id="adj_rider_id" name="rider_id" class="form-control select2" required>
                                    <option value="">Select Rider</option>
                                    @foreach ($riders as $rider)
                                        <option value="{{ $rider->id }}">{{ $rider->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="type" class="d-block">Type</label>
                                <select id="type" name="type" class="form-control select2" required>
                                    <option value="credit">Credit</option>
                                    <option value="debit">Debit</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="amount" class="d-block">Amount</label>
                                <input id="amount" type="number" step="0.01" name="amount" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label for="note" class="d-block">Note</label>
                                <input id="note" type="text" name="note" class="form-control" maxlength="255" placeholder="Optional note">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">Apply</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="mb-3">Wallet Transactions</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Rider</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Parcel</th>
                                    <th>Description</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->rider_name ?? 'N/A' }}</td>
                                        <td>
                                            @if ($transaction->type === 'credit')
                                                <span class="badge badge-success">Credit</span>
                                            @else
                                                <span class="badge badge-danger">Debit</span>
                                            @endif
                                        </td>
                                        <td>{{ settings()->currency }} {{ number_format((float) ($transaction->amount ?? 0), 2) }}</td>
                                        <td>{{ $transaction->parcel_id ?? '-' }}</td>
                                        <td>{{ $transaction->description ?? '-' }}</td>
                                        <td>{{ $transaction->created_at }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No wallet transactions found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $transactions->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .select2-container {
            width: 100% !important;
        }
    </style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $(".select2").select2();
    });

</script>
@endpush
