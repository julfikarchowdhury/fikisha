@extends('backend.partials.master')
@section('title')
Rider Withdraw Requests
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
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">Rider Withdraw Requests</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('levels.list') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="status">Status</label>
                                <select id="status" name="status" class="form-control">
                                    <option value="">All</option>
                                    <option value="pending" @selected($request->status === 'pending')>Pending</option>
                                    <option value="approved" @selected($request->status === 'approved')>Approved</option>
                                    <option value="rejected" @selected($request->status === 'rejected')>Rejected</option>
                                    <option value="paid" @selected($request->status === 'paid')>Paid</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="rider_id">Rider ID</label>
                                <input id="rider_id" name="rider_id" type="text" class="form-control" value="{{ $request->rider_id }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button class="btn btn-primary mr-2" type="submit">Filter</button>
                                <a class="btn btn-secondary" href="{{ route('rider.withdraw.requests.index') }}">Clear</a>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Rider</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Requested At</th>
                                    <th>Approved At</th>
                                    <th>Note</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($requests as $withdraw)
                                    <tr>
                                        <td>{{ $withdraw->id }}</td>
                                        <td>
                                            {{ $withdraw->rider?->name ?? 'N/A' }}
                                            <div class="text-muted small">#{{ $withdraw->rider_id }}</div>
                                        </td>
                                        <td>{{ settings()->currency }} {{ number_format($withdraw->amount, 2) }}</td>
                                        <td>{{ ucfirst($withdraw->status) }}</td>
                                        <td>{{ optional($withdraw->requested_at)->format('Y-m-d H:i') }}</td>
                                        <td>{{ optional($withdraw->approved_at)->format('Y-m-d H:i') }}</td>
                                        <td>{{ $withdraw->note }}</td>
                                        <td>
                                            @if ($withdraw->status === 'pending')
                                                <form method="POST" action="{{ route('rider.withdraw.requests.approve', $withdraw->id) }}" class="d-inline">
                                                    @csrf
                                                    <button class="btn btn-sm btn-success" type="submit">Approve</button>
                                                </form>
                                                <form method="POST" action="{{ route('rider.withdraw.requests.reject', $withdraw->id) }}" class="d-inline">
                                                    @csrf
                                                    <input type="text" name="note" class="form-control form-control-sm d-inline-block" style="width: 140px;" placeholder="Reject note">
                                                    <button class="btn btn-sm btn-danger" type="submit">Reject</button>
                                                </form>
                                            @else
                                                <span class="text-muted">No actions</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No withdrawal requests found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $requests->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
