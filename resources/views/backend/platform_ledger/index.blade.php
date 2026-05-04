@extends('backend.partials.master')
@section('title')
    Platform Ledger
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}" class="breadcrumb-link">{{ __('levels.dashboard') }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Platform Ledger</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted">Total Credits</div>
                    <div class="h4 mb-0">{{ settings()->currency }} {{ number_format((float) $credits, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted">Total Debits</div>
                    <div class="h4 mb-0">{{ settings()->currency }} {{ number_format((float) $debits, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted">Net</div>
                    <div class="h4 mb-0">{{ settings()->currency }} {{ number_format((float) $net, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.platform-ledger.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="type">Type</label>
                        <input type="text" id="type" name="type" value="{{ $request->type }}" class="form-control" placeholder="commission_reversal">
                    </div>
                    <div class="col-md-3">
                        <label for="direction">Direction</label>
                        <select id="direction" name="direction" class="form-control">
                            <option value="">All</option>
                            <option value="credit" @selected($request->direction === 'credit')>Credit</option>
                            <option value="debit" @selected($request->direction === 'debit')>Debit</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="parcel_id">Parcel ID</label>
                        <input type="text" id="parcel_id" name="parcel_id" value="{{ $request->parcel_id }}" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label for="from_date">From</label>
                        <input type="date" id="from_date" name="from_date" value="{{ $request->from_date }}" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <label for="to_date">To</label>
                        <input type="date" id="to_date" name="to_date" value="{{ $request->to_date }}" class="form-control">
                    </div>
                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary btn-sm">{{ __('levels.filter') }}</button>
                        <a href="{{ route('admin.platform-ledger.index') }}" class="btn btn-secondary btn-sm">{{ __('levels.clear') }}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Parcel</th>
                            <th>Type</th>
                            <th>Direction</th>
                            <th>Amount</th>
                            <th>Reference</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($transactions as $tx)
                            <tr>
                                <td>{{ $tx->id }}</td>
                                <td>{{ $tx->parcel_id ?? '-' }}</td>
                                <td>{{ $tx->type }}</td>
                                <td>{{ ucfirst($tx->direction) }}</td>
                                <td>{{ settings()->currency }} {{ number_format((float) $tx->amount, 2) }}</td>
                                <td>{{ $tx->reference_id ?? '-' }}</td>
                                <td>{{ $tx->created_at?->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No transactions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
