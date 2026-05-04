@extends('backend.partials.master')
@section('title')
    Disputes
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
                            <li class="breadcrumb-item active" aria-current="page">Disputes</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.disputes.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="">All</option>
                            <option value="open" @selected($request->status == 'open')>Open</option>
                            <option value="under_review" @selected($request->status == 'under_review')>Under Review</option>
                            <option value="resolved" @selected($request->status == 'resolved')>Resolved</option>
                            <option value="rejected" @selected($request->status == 'rejected')>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="raised_by">Raised By</label>
                        <select id="raised_by" name="raised_by" class="form-control">
                            <option value="">All</option>
                            <option value="sender" @selected($request->raised_by == 'sender')>Sender</option>
                            <option value="rider" @selected($request->raised_by == 'rider')>Rider</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="from_date">From</label>
                        <input type="date" id="from_date" name="from_date" value="{{ $request->from_date }}" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label for="to_date">To</label>
                        <input type="date" id="to_date" name="to_date" value="{{ $request->to_date }}" class="form-control">
                    </div>
                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary btn-sm">{{ __('levels.filter') }}</button>
                        <a href="{{ route('admin.disputes.index') }}" class="btn btn-secondary btn-sm">{{ __('levels.clear') }}</a>
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
                            <th>Raised By</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($disputes as $dispute)
                            <tr>
                                <td>{{ $dispute->id }}</td>
                                <td>
                                    {{ optional($dispute->parcel)->tracking_id ?? '-' }}
                                </td>
                                <td>{{ ucfirst($dispute->raised_by) }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $dispute->reason_type)) }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $dispute->status)) }}</td>
                                <td>{{ $dispute->created_at?->format('Y-m-d') }}</td>
                                <td>
                                    <a href="{{ route('admin.disputes.show', $dispute->id) }}" class="btn btn-sm btn-primary">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No disputes found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $disputes->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
