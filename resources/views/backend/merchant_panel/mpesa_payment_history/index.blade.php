@extends('backend.partials.master')
@section('title')
    M-Pesa Payment History
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
                                <li class="breadcrumb-item">
                                    <a href="{{ route('merchant-panel.mpesa.payment-history.index') }}" class="breadcrumb-link">M-Pesa Payment History</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="#" class="breadcrumb-link active">{{ __('levels.list') }}</a>
                                </li>
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
                        <form action="{{ route('merchant-panel.mpesa.payment-history.index') }}" method="GET">
                            <div class="row">
                                <div class="form-group col-12 col-md-3">
                                    <label for="date">{{ __('parcel.date') }}</label>
                                    <input type="text" id="date" name="date" class="form-control date_range_picker"
                                        autocomplete="off" value="{{ old('date', $request->date) }}">
                                </div>
                                <div class="form-group col-12 col-md-2">
                                    <label for="status">{{ __('levels.status') }}</label>
                                    <select id="status" name="status" class="form-control">
                                        <option value="">{{ __('levels.select') }} {{ __('levels.status') }}</option>
                                        <option value="pending" @selected(($request->status ?? '') === 'pending')>Pending</option>
                                        <option value="success" @selected(($request->status ?? '') === 'success')>Success</option>
                                        <option value="failed" @selected(($request->status ?? '') === 'failed')>Failed</option>
                                    </select>
                                </div>
                                <div class="form-group col-12 col-md-2">
                                    <label for="payer">Payer</label>
                                    <select id="payer" name="payer" class="form-control">
                                        <option value="">All</option>
                                        <option value="sender" @selected(($request->payer ?? '') === 'sender')>Sender</option>
                                        <option value="receiver" @selected(($request->payer ?? '') === 'receiver')>Receiver</option>
                                    </select>
                                </div>
                                <div class="form-group col-12 col-md-3">
                                    <label for="search">Search</label>
                                    <input type="text" id="search" name="search" class="form-control"
                                        placeholder="Tracking / Checkout ID / Phone"
                                        value="{{ old('search', $request->search) }}">
                                </div>
                                <div class="form-group col-12 col-md-2 pt-4">
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        <i class="fa fa-filter"></i> {{ __('levels.filter') }}
                                    </button>
                                    <a href="{{ route('merchant-panel.mpesa.payment-history.index') }}" class="btn btn-sm btn-secondary">
                                        <i class="fa fa-eraser"></i> {{ __('levels.clear') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                        <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">M-Pesa Payment History</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{ __('###') }}</th>
                                        <th>Date</th>
                                        <th>Tracking ID</th>
                                        <th>Payer</th>
                                        <th>Phone</th>
                                        <th>Amount</th>
                                        <th>{{ __('levels.status') }}</th>
                                        <th>Checkout Request ID</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($payments as $payment)
                                        @php
                                            $payer = data_get($payment->parcel_payload, 'who_pays');
                                            if (is_numeric($payer)) {
                                                $payer = (int) $payer === \App\Enums\WhoPays::RECIPIENT ? 'receiver' : 'sender';
                                            }
                                            if (!$payer && $payment->parcel) {
                                                $payer = (int) $payment->parcel->who_pays === \App\Enums\WhoPays::RECIPIENT ? 'receiver' : 'sender';
                                            }
                                            $badgeClass = 'badge-warning';
                                            if ($payment->status === 'success') {
                                                $badgeClass = 'badge-success';
                                            } elseif ($payment->status === 'failed') {
                                                $badgeClass = 'badge-danger';
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ $loop->iteration + (($payments->currentPage() - 1) * $payments->perPage()) }}</td>
                                            <td>{{ dateFormat($payment->created_at) }}</td>
                                            <td>
                                                @if($payment->parcel)
                                                    <a href="{{ route('merchant-panel.parcel.details', $payment->parcel->id) }}">
                                                        #{{ $payment->parcel->tracking_id }}
                                                    </a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ ucfirst($payer ?? 'sender') }}</td>
                                            <td>{{ $payment->phone ?? 'N/A' }}</td>
                                            <td>{{ settings()->currency }}{{ number_format((float) $payment->amount, 2) }}</td>
                                            <td><span class="badge badge-pill {{ $badgeClass }}">{{ ucfirst($payment->status) }}</span></td>
                                            <td class="text-break">{{ $payment->checkout_request_id ?? 'N/A' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">No payment history found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="pb-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <span>{{ $payments->links() }}</span>
                        <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
                            {!! __('Showing') !!}
                            <span class="font-medium">{{ $payments->firstItem() ?? 0 }}</span>
                            {!! __('to') !!}
                            <span class="font-medium">{{ $payments->lastItem() ?? 0 }}</span>
                            {!! __('of') !!}
                            <span class="font-medium">{{ $payments->total() }}</span>
                            {!! __('results') !!}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection()

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush

@push('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="{{ static_asset('backend/js/date-range-picker/date-range-picker-custom.js') }}"></script>
@endpush
