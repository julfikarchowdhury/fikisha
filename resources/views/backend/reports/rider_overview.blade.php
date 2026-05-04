@extends('backend.partials.master')
@section('title')
    Rider Overview
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
                            <li class="breadcrumb-item active" aria-current="page">Rider Overview</li>
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
                    <form method="GET" action="{{ route('rider.overview') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="from_date">From</label>
                                <input id="from_date" type="date" name="from_date" class="form-control" value="{{ $request->from_date }}">
                            </div>
                            <div class="col-md-3">
                                <label for="to_date">To</label>
                                <input id="to_date" type="date" name="to_date" class="form-control" value="{{ $request->to_date }}">
                            </div>
                            <div class="col-md-4">
                                <label for="rider_id">Rider</label>
                                <select id="rider_id" name="rider_id" class="form-control select2">
                                    <option value="">All Riders</option>
                                    @foreach ($allRiders as $rider)
                                        <option value="{{ $rider->id }}" @selected($request->rider_id == $rider->id)>{{ $rider->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">{{ __('levels.filter') }}</button>
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
                    <h6 class="mb-2">Total Riders</h6>
                    <h4 class="mb-0">{{ $summary['total_riders'] ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-2">Total Deliveries</h6>
                    <h4 class="mb-0">{{ $summary['total_deliveries'] ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-2">Total Earnings</h6>
                    <h4 class="mb-0">{{ settings()->currency }} {{ number_format((float) ($summary['total_earnings'] ?? 0), 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-2">Pending Withdrawals</h6>
                    <h4 class="mb-0">{{ settings()->currency }} {{ number_format((float) ($summary['total_pending_withdraw'] ?? 0), 2) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="mb-2">Wallet Balance</h6>
                    <h4 class="mb-0">{{ settings()->currency }} {{ number_format((float) ($summary['total_wallet_balance'] ?? 0), 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Rider</th>
                                    <th>Phone</th>
                                    <th>Vehicle</th>
                                    <th>Status</th>
                                    <th>Availability</th>
                                    <th>Total Deliveries</th>
                                    <th>Total Earnings</th>
                                    <th>Wallet Balance</th>
                                    <th>Pending Withdraw</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($riders as $rider)
                                    @php
                                        $statusLabel = $statusLabels[$rider->rider_status ?? \App\Enums\RiderStatus::APPROVED] ?? 'Unknown';
                                        $statusClass = 'badge-secondary';
                                        if (in_array((int) $rider->rider_status, [\App\Enums\RiderStatus::APPROVED], true)) {
                                            $statusClass = 'badge-success';
                                        } elseif (in_array((int) $rider->rider_status, [\App\Enums\RiderStatus::UNDER_REVIEW, \App\Enums\RiderStatus::PENDING_KYC], true)) {
                                            $statusClass = 'badge-warning';
                                        } elseif (in_array((int) $rider->rider_status, [\App\Enums\RiderStatus::SUSPENDED, \App\Enums\RiderStatus::BLOCKED], true)) {
                                            $statusClass = 'badge-danger';
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $rider->name }}</td>
                                        <td>{{ $rider->mobile }}</td>
                                        <td>{{ $rider->vehicle_type ?? '-' }}</td>
                                        <td><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                                        <td>
                                            @if ((int) $rider->is_available === 1)
                                                <span class="badge badge-success">Online</span>
                                            @else
                                                <span class="badge badge-secondary">Offline</span>
                                            @endif
                                        </td>
                                        <td>{{ $rider->total_deliveries ?? 0 }}</td>
                                        <td>{{ settings()->currency }} {{ number_format((float) ($rider->total_earnings ?? 0), 2) }}</td>
                                        <td>{{ settings()->currency }} {{ number_format((float) ($rider->balance ?? 0), 2) }}</td>
                                        <td>{{ settings()->currency }} {{ number_format((float) ($rider->pending_withdraw_amount ?? 0), 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No riders found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $riders->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
