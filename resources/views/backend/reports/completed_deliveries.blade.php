@extends('backend.partials.master')
@section('title')
    Total Completed Deliveries
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
                            <li class="breadcrumb-item active" aria-current="page">Total Completed Deliveries</li>
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
                    <form method="GET" action="{{ route('reports.completed.deliveries') }}">
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
                                    @foreach ($riders as $rider)
                                        <option value="{{ $rider->id }}" @selected($request->rider_id == $rider->id)>{{ $rider->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100" style="height: 39px;">{{ __('levels.filter') }}</button>
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
                    <h6 class="mb-2" style="font-size: 14px;">Total Completed</h6>
                    <h4 class="mb-0">{{ $totalCompleted }}</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Completed Deliveries</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Parcel ID</th>
                                    <th>Rider</th>
                                    <th>Delivered Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($parcels as $parcel)
                                    <tr>
                                        <td>{{ $parcel->tracking_id ?? $parcel->id }}</td>
                                        <td>{{ $parcel->rider_name ?? 'N/A' }}</td>
                                        <td>{{ $parcel->updated_at }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No completed deliveries found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $parcels->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- css  -->
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
<!-- js  -->
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
 @endpush



