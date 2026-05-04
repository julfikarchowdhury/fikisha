@extends('backend.partials.master')
@section('title')
    {{ __('parcel.title') }} {{ __('levels.view') }}
@endsection
@section('maincontent')
    @php
        $currency = settings()->currency;
        $events = \App\Models\Backend\ParcelEvent::where('parcel_id', $parcel->id)->latest()->take(15)->get();
        $riderUser = \App\Models\User::find($parcel->delivery_man_id);
        if (!$riderUser) {
            $assignedEvent = \App\Models\Backend\ParcelEvent::where('parcel_id', $parcel->id)
                ->whereIn('parcel_status', [
                    \App\Enums\ParcelStatus::MARKETPLACE_ACCEPTED,
                    \App\Enums\ParcelStatus::MARKETPLACE_PICKED_UP,
                    \App\Enums\ParcelStatus::MARKETPLACE_DELIVERED,
                    \App\Enums\ParcelStatus::DELIVERY_MAN_ASSIGN,
                    \App\Enums\ParcelStatus::DELIVERY_RE_SCHEDULE,
                ])
                ->latest()
                ->first();
            $riderUser = optional(optional($assignedEvent)->deliveryman)->user;
        }

        $whoPaysLabel = __('levels.sender');
        if ((int) $parcel->who_pays == \App\Enums\WhoPays::RECIPIENT) {
            $whoPaysLabel = __('levels.recipient');
        }

        $isPaid = (int) $parcel->payment_status === \App\Enums\InvoiceStatus::PAID;
        $paymentStatusLabel = $isPaid ? 'Paid' : 'Unpaid';
        $paymentStatusClass = $isPaid ? 'badge-success' : 'badge-warning';

        $mainItem = (array) ($parcel->cbm_details ?? []);
        $mainItemTotalWeight = (float) ($mainItem['total_weight'] ?? 0);
        $mainItemTotalCbm = (float) ($mainItem['total_cbm'] ?? 0);
        $mainItemParcelValue = (float) ($mainItem['parcel_value'] ?? 0);
        $itemsWeight = (float) $parcel->items->sum('total_weight');
        $itemsCbm = (float) $parcel->items->sum('total_cbm');
        $itemsParcelValue = (float) $parcel->items->sum('parcel_value');
        $totalWeight = $mainItemTotalWeight + $itemsWeight;
        $totalCbm = $mainItemTotalCbm + $itemsCbm;
        $totalParcelValue = $mainItemParcelValue + $itemsParcelValue;
    @endphp

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
                                    <a href="{{ route('merchant-panel.parcel.index') }}" class="breadcrumb-link">{{ __('parcel.title') }}</a>
                                </li>
                                <li class="breadcrumb-item active">{{ __('levels.details') }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-3">
                <div class="card">
                    <div class="card-body d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <h4 class="mb-1">{{ __('invoice.invoice') }} #{{ $parcel->invoice_no }}</h4>
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span>{!! $parcel->parcel_status !!}</span>
                                <span class="badge {{ $paymentStatusClass }}">{{ $paymentStatusLabel }}</span>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('merchant-panel.parcel.logs', $parcel->id) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                {{ __('parcel.tracking') }} <i class="fa-solid fa-arrow-up-right-from-square"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-3">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('levels.route') }}</h5>
                        <small class="text-muted">{{ __('levels.total_distance') }}: {{ number_format((float) $parcel->distance_km, 2) }} km</small>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>{{ __('levels.pickup_location') }}:</strong><br>{{ $parcel->pickup_location ?? '-' }}</p>
                        <p class="mb-0"><strong>{{ __('levels.drop_location') }}:</strong><br>{{ $parcel->drop_location ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-3">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('deliveryman.title') }}</h5>
                    </div>
                    <div class="card-body">
                        @if ($riderUser)
                            <div class="d-flex align-items-center">
                                @if ($riderUser->image)
                                    <img src="{{ $riderUser->image }}" alt="rider" class="rounded me-3" width="48" height="48">
                                @endif
                                <div>
                                    <div><strong>{{ $riderUser->name }}</strong></div>
                                    <div class="text-muted">{{ $riderUser->email ?? '-' }}</div>
                                    <div class="text-muted">{{ $riderUser->mobile ?? '-' }}</div>
                                </div>
                            </div>
                        @else
                            <p class="mb-0 text-muted">Not assigned yet</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-3">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('levels.sender_info') }}</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>{{ __('levels.name') }}:</strong> {{ trim(($parcel->sender_first_name ?? '') . ' ' . ($parcel->sender_last_name ?? '')) ?: '-' }}</p>
                        <p class="mb-1"><strong>{{ __('levels.phone') }}:</strong> {{ $parcel->sender_phone ?? '-' }}</p>
                        <p class="mb-1"><strong>{{ __('levels.email') }}:</strong> {{ $parcel->sender_email ?? '-' }}</p>
                        <p class="mb-0"><strong>{{ __('levels.address') }}:</strong> {{ $parcel->pickup_address ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-3">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('levels.recipient_info') }}</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>{{ __('levels.name') }}:</strong> {{ trim(($parcel->customer_first_name ?? '') . ' ' . ($parcel->customer_last_name ?? '')) ?: '-' }}</p>
                        <p class="mb-1"><strong>{{ __('levels.phone') }}:</strong> {{ $parcel->customer_phone ?? '-' }}</p>
                        <p class="mb-1"><strong>{{ __('levels.email') }}:</strong> {{ $parcel->receiver_email ?? '-' }}</p>
                        <p class="mb-1"><strong>{{ __('levels.address') }}:</strong> {{ $parcel->customer_address ?? '-' }}</p>
                        @if (!empty($parcel->receiver_mpesa_phone))
                            <p class="mb-0"><strong>{{ __('levels.phone') }} (M-Pesa):</strong> {{ $parcel->receiver_mpesa_phone }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-3">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Payment Summary</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm mb-0">
                            <tbody>
                                <tr>
                                    <td>Who Pays</td>
                                    <td class="text-end">{{ $whoPaysLabel }}</td>
                                </tr>
                                <tr>
                                    <td>Payment Status</td>
                                    <td class="text-end">{{ $paymentStatusLabel }}</td>
                                </tr>
                                <tr>
                                    <td>{{ __('levels.delivery_charge') }}</td>
                                    <td class="text-end">{{ $currency }} {{ number_format((float) $parcel->delivery_charge, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>{{ __('levels.total_cost') }}</strong></td>
                                    <td class="text-end"><strong>{{ $currency }} {{ number_format((float) $parcel->total_delivery_amount, 2) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-3">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('levels.delivery_info') }}</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-1"><strong>{{ __('levels.weight') }}:</strong> {{ number_format($totalWeight, 2) }} kg</p>
                        <p class="mb-1"><strong>{{ __('parcel.total_cubmeter') }}:</strong> {{ number_format($totalCbm, 3) }} m<sup>3</sup></p>
                        <p class="mb-1"><strong>{{ __('parcel.parcel_value') }}:</strong> {{ $currency }} {{ number_format($totalParcelValue, 2) }}</p>
                        <p class="mb-1"><strong>{{ __('levels.note') }}:</strong> {{ $parcel->note ?: '-' }}</p>
                        @if (!empty($parcel->parcel_file))
                            <p class="mb-0">
                                <strong>{{ __('levels.file') }}:</strong>
                                <a href="{{ \Illuminate\Support\Str::startsWith($parcel->parcel_file, ['http://', 'https://']) ? $parcel->parcel_file : asset($parcel->parcel_file) }}" target="_blank">
                                    {{ __('levels.view') }}
                                </a>
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-12 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('levels.parcel_items') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('levels.details') }}</th>
                                        <th>{{ __('parcel.quantity') }}</th>
                                        <th>{{ __('parcel.total_weight') }}</th>
                                        <th>{{ __('parcel.total_cubmeter') }}</th>
                                        <th>{{ __('parcel.parcel_value') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php($row = 1)
                                    @if (!empty($mainItem))
                                        <tr>
                                            <td>{{ $row++ }}</td>
                                            <td>{{ $mainItem['content_parcel'] ?? '-' }}</td>
                                            <td>{{ $mainItem['quantity'] ?? 1 }}</td>
                                            <td>{{ number_format((float) ($mainItem['total_weight'] ?? 0), 2) }} kg</td>
                                            <td>{{ number_format((float) ($mainItem['total_cbm'] ?? 0), 3) }} m<sup>3</sup></td>
                                            <td>{{ $currency }} {{ number_format((float) ($mainItem['parcel_value'] ?? 0), 2) }}</td>
                                        </tr>
                                    @endif
                                    @foreach ($parcel->items as $item)
                                        <tr>
                                            <td>{{ $row++ }}</td>
                                            <td>{{ $item->content_parcel ?? '-' }}</td>
                                            <td>{{ $item->quantity ?? 1 }}</td>
                                            <td>{{ number_format((float) $item->total_weight, 2) }} kg</td>
                                            <td>{{ number_format((float) $item->total_cbm, 3) }} m<sup>3</sup></td>
                                            <td>{{ $currency }} {{ number_format((float) $item->parcel_value, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    @if (empty($mainItem) && $parcel->items->isEmpty())
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No data found</td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">{{ __('parcel.total') }}</th>
                                        <th>{{ number_format($totalWeight, 2) }} kg</th>
                                        <th>{{ number_format($totalCbm, 3) }} m<sup>3</sup></th>
                                        <th>{{ $currency }} {{ number_format($totalParcelValue, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('levels.status') }} History</h5>
                    </div>
                    <div class="card-body">
                        @if ($events->isEmpty())
                            <p class="mb-0 text-muted">No data found</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>{{ __('levels.status') }}</th>
                                            <th>{{ __('levels.date') }}</th>
                                            <th>{{ __('levels.note') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($events as $event)
                                            <tr>
                                                <td>{{ trans('parcelStatusShow.' . $event->parcel_status) }}</td>
                                                <td>{{ @dateFormat($event->created_at) }} {{ @date('h:i a', strtotime($event->created_at)) }}</td>
                                                <td>{{ $event->note ?: '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection()
