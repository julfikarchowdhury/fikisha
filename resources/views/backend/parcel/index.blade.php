@extends('backend.partials.master')
@section('title')
{{ __('parcel.title') }} {{ __('levels.list') }}
@endsection
@section('maincontent')
<!-- wrapper  -->
<div class="container-fluid  dashboard-content">
    <!-- page header -->
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}" class="breadcrumb-link">
                                    {{ __('menus.dashboard') }}
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('parcel.index') }}" class="breadcrumb-link">
                                    {{ __('parcel.title') }}
                                </a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#" class="breadcrumb-link active">
                                    {{ __('levels.list') }}
                                </a>
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- end page header -->
    <div class="row">
        <!-- data table  -->
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('parcel.filter') }}" method="GET">
                        <div class="row">
                            <div class="form-group col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2">
                                <label for="parcel_date">{{ __('parcel.date') }}</label>
                                <input type="text" autocomplete="off" id="date" name="parcel_date" placeholder="Enter Date" class="form-control date_range_picker" value="{{ old('parcel_date', $request->parcel_date) }}">
                                @error('parcel_date')
                                <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2">
                                <label for="parcelStatus">{{ __('parcel.status') }}</label>
                                <select style="width: 100%" id="parcelStatus" name="parcel_status" class="form-control @error('parcel_status') is-invalid @enderror">
                                    <option value="">{{ __('parcel.all') }}</option>
                                    @foreach ([
                                        \App\Enums\ParcelStatus::MARKETPLACE_PENDING,
                                        \App\Enums\ParcelStatus::MARKETPLACE_SENDER_PAYMENT_AWAITING,
                                        \App\Enums\ParcelStatus::MARKETPLACE_RECEIVER_PAYMENT_AWAITING,
                                        \App\Enums\ParcelStatus::MARKETPLACE_ACCEPTED,
                                        \App\Enums\ParcelStatus::MARKETPLACE_PICKED_UP,
                                        \App\Enums\ParcelStatus::MARKETPLACE_DELIVERED,
                                        \App\Enums\ParcelStatus::MARKETPLACE_CANCELLED,
                                    ] as $key)
                                    <option value="{{ $key }}" {{ old('parcel_status', $request->parcel_status) == $key ? 'selected' : '' }}>
                                        {{ trans('parcelStatusShow.' . $key) }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('parcel_status')
                                <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2">
                                <label for="parcelMerchantid">{{ __('parcel.merchant') }}</label>
                                <select style="width: 100%" id="parcelMerchantid" name="parcel_merchant_id" class="form-control @error('parcel_merchant_id') is-invalid @enderror" data-url="{{ route('parcel.merchant.shops') }}">
                                    <option value=""> {{ __('menus.select') }} {{ __('merchant.title') }}</option>
                                </select>
                                @error('parcel_merchant_id')
                                <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2">
                                <label for="payment_status">{{ __('parcel.payment') }}</label>
                                <select id="payment_status" name="payment_status" class="form-control select2">
                                    <option value="">{{ __('levels.select') }} {{ __('parcel.payment') }}</option>
                                    <option value="{{ App\Enums\InvoiceStatus::UNPAID }}" @selected($request->payment_status == App\Enums\InvoiceStatus::UNPAID)>{{ __('Unpaid') }}</option>
                                    <option value="{{ App\Enums\InvoiceStatus::PROCESSING }}" @selected($request->payment_status == App\Enums\InvoiceStatus::PROCESSING)>{{ __('Processing') }}</option>
                                    <option value="{{ App\Enums\InvoiceStatus::PAID }}" @selected($request->payment_status == App\Enums\InvoiceStatus::PAID)>{{ __('Paid') }}</option>
                                </select>
                            </div>

                            <div class="form-group col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2 pt-1 pl-0">
                                <div class="col-12 pt-4 d-flex justify-content text-right">
                                    <button type="submit" class="btn btn-sm btn-space btn-primary">
                                        <i class="fa fa-filter"></i> {{ __('levels.filter') }}
                                    </button>
                                    <a href="{{ route('parcel.index') }}" class="btn btn-sm btn-space btn-secondary">
                                        <i class="fa fa-eraser"></i> {{ __('levels.clear') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <div class="d-flex align-items-center gap-3">
                        <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('parcel.title') }}</h4>
                        <form action="{{ route('parcel.specific.search') }}" method="get">
                            <div class="d-flex parcelsearchFlex">
                                <input id="Psearch" class="form-control parcelSearchs group-input d-lg-block "
                                    name="search" type="text" placeholder="{{ __('levels.search') }}..."
                                    value="{{ $request->search }}">
                                <button type="submit" class="btn btn-sm btn-space btn-primary group-btn  d-lg-block"
                                    style="margin-bottom: 0px;margin-left:0px!important; height: 39px;"><i class="fa fa-filter"></i>
                                    {{ __('levels.search') }}</button>
                            </div>
                        </form>
                        <div class="d-lg-none">
                            <form action="{{ route('parcel.specific.search') }}" method="get">
                                <div class="d-flex parcelsearchFlex ml-0">
                                    <input id="Psearch" class="parcelml-0 form-control  group-input w-100 "
                                        name="search" type="text" placeholder="{{ __('levels.search') }}..."
                                        value="{{ $request->search }}">
                                    <button type="submit" class="btn btn-sm btn-space btn-primary group-btn"
                                        style="margin-bottom: 0px;margin-left:0px!important; height: 39px;"><i class="fa fa-filter"></i>
                                        {{ __('levels.search') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @if (hasPermission('parcel_create'))
                        <div class="col-12 col-xl-6 mt-2 mt-lg-2 mt-xl-0">
                            <div class="text-right d-flex justify-content-end parcel-index-bulk">
                                {{-- multiple parcel label print --}}
                                <form action="{{ route('parcel.multiple.print-label') }}" method="get"
                                    target="_blank" id="print_label_form">
                                    @csrf
                                    <div id="print_label_content"></div>
                                    <button type="submit" class="btn btn-sm btn-primary mr-2 multiplelabelprint" data-parcels='' style="display: none">{{ __('levels.print_label') }}</button>
                                </form>
                                {{-- end multiple parcel label print --}}
                                <a href="{{ route('parcel.parcelDeliveryMan') }}" target="_blank" class="btn btn-sm btn-secondary mr-1 " data-toggle="tooltip" data-placement="top" title="Parcel Map">
                                    <i class="fa fa-map-location"></i> {{ __('parcel.map') }}
                                </a>
                                <a href="{{ route('parcel.create') }}" class="btn btn-sm btn-primary ml-1" data-toggle="tooltip" data-placement="top" title="Add">
                                    <i class="fa fa-plus"></i> {{ __('parcel.create_order') }}
                                </a>
                            </div>
                        </div>
                        @endif
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills mb-3 parcel-status-btn" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="mt-2 nav-link btn btn-active @if (!$request->parcel_status) active @endif me-2" href="{{ route('parcel.index') }}">{{ __('parcel.all') }}
                                <span class="badge badge-primary">
                                    @php
                                    $allOrder = \App\Models\Backend\Parcel::query()->count();
                                    @endphp
                                    {{ $allOrder }}
                                </span>
                            </a>
                        </li>
                        @foreach ([
                            \App\Enums\ParcelStatus::MARKETPLACE_PENDING,
                            \App\Enums\ParcelStatus::MARKETPLACE_SENDER_PAYMENT_AWAITING,
                            \App\Enums\ParcelStatus::MARKETPLACE_RECEIVER_PAYMENT_AWAITING,
                            \App\Enums\ParcelStatus::MARKETPLACE_ACCEPTED,
                            \App\Enums\ParcelStatus::MARKETPLACE_PICKED_UP,
                            \App\Enums\ParcelStatus::MARKETPLACE_DELIVERED,
                            \App\Enums\ParcelStatus::MARKETPLACE_CANCELLED,
                        ] as $key)
                            <li class="nav-item" role="presentation">
                                <a class="mt-2 nav-link btn btn-active {{ (int) $request->parcel_status === $key ? 'active' : '' }} me-2"
                                    href="{{ route('parcel.filter', ['parcel_status' => $key]) }}">
                                    {{ trans('parcelStatusShow.' . $key) }}
                                    <span class="badge badge-primary">
                                        {{ \App\Models\Backend\Parcel::query()->where('status', $key)->count() }}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="table-responsive">
                        <table id="table" class="table parcelTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="parcel-index permission-check-box">
                                        <input type="checkbox" id="tick-all" class="form-check-input" />
                                    </th>
                                    <th>{{ __('###') }}</th>
                                    <th>{{ __('parcel.tracking_id') }}</th>
                                    <th>{{ __('parcel.merchant') }}</th>
                                    <th>{{ __('parcel.recipient_info') }}</th>
                                    <th>{{ __('parcel.location') }}</th>
                                    <th>Marketplace Payment</th>
                                    <th>{{ __('parcel.status') }}</th>
                                    @if (hasPermission('parcel_status_update') == true)
                                    <th>{{ __('parcel.status_update') }}</th>
                                    @endif
                                    <th>{{ __('parcel.payment') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $i = 1;
                                @endphp
                                @foreach ($parcels as $parcel)
                                <tr>
                                    <td class="parcel-index permission-check-box">
                                        <input type="checkbox" name="parcels[][{{ @$parcel->id }}]" value="{{ @$parcel->id }}" class="common-key form-check-input" />
                                    </td>
                                    <td>
                                        <div class="row">
                                            <button tabindex="-1" data-toggle="dropdown" type="button"
                                                class="btn btn-sm ml-2 bnone"><i class="fa fa-cogs"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a href="{{ route('parcel.details', $parcel->id) }}" class="dropdown-item">
                                                    <i class="fa fa-eye" aria-hidden="true"></i> {{ __('levels.view') }}
                                                </a>
                                                <a href="{{ route('parcel.logs', $parcel->id) }}" class="dropdown-item">
                                                    <i class="fas fa-history" aria-hidden="true"></i> {{ __('levels.parcel_logs') }}
                                                </a>
                                                <a href="{{ route('parcel.placed', $parcel->id) }}" class="dropdown-item">
                                                    <i class="fas fa-box" aria-hidden="true"></i> {{ __('levels.order_placed') }}
                                                </a>
                                                <a href="{{ route('parcel.clone', $parcel->id) }}" class="dropdown-item">
                                                    <i class="fas fa-clone" aria-hidden="true"></i> {{ __('levels.clone') }}
                                                </a>
                                                <a href="{{ route('parcel.print', $parcel->id) }}" target="_blank" class="dropdown-item">
                                                    <i class="fas fa-print" aria-hidden="true"></i> {{ __('levels.print') }}
                                                </a>
                                                <a href="{{ route('parcel.print-label', $parcel->id) }}" target="_blank" class="dropdown-item">
                                                    <i class="fas fa-print" aria-hidden="true"></i>
                                                    {{ __('levels.print_label') }}
                                                </a>
                                                @if (
                                                    !in_array($parcel->status, [
                                                        \App\Enums\ParcelStatus::DELIVERED,
                                                        \App\Enums\ParcelStatus::PARTIAL_DELIVERED,
                                                        \App\Enums\ParcelStatus::RETURN_RECEIVED_BY_MERCHANT,
                                                        \App\Enums\ParcelStatus::MARKETPLACE_DELIVERED,
                                                        \App\Enums\ParcelStatus::MARKETPLACE_CANCELLED,
                                                    ])
                                                )
                                                @if (hasPermission('parcel_update') == true)
                                                <a href="{{ route('parcel.edit', $parcel->id) }}" class="dropdown-item">
                                                    <i class="fas fa-edit" aria-hidden="true"></i> {{ __('levels.edit') }}
                                                </a>
                                                @endif
                                                @if (hasPermission('parcel_delete'))
                                                <form id="delete" value="Test" action="{{ route('parcel.delete', $parcel->id) }}" method="POST" data-title="{{ __('delete.parcel') }}">
                                                    @method('DELETE')
                                                    @csrf
                                                    <input type="hidden" name="" value="Parcel" id="deleteTitle">
                                                    <button type="submit" class="dropdown-item">
                                                        <i class="fa fa-trash" aria-hidden="true"></i> {{ __('levels.delete') }}
                                                    </button>
                                                </form>
                                                @endif
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        #{{ $parcel->tracking_id }}<br />
                                        @php
                                            $trackingUrl = !empty($parcel->tracking_token)
                                                ? route('track.public', $parcel->tracking_token)
                                                : route('parcel.logs', $parcel->id);
                                        @endphp
                                        <a href="{{ $trackingUrl }}" class="btn btn-outline-primary mt-2" target="_blank">
                                            {{ __('parcel.tracking') }} <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        </a>
                                    </td>
                                    <td>
                                        <p style="margin-bottom: 0px!important">
                                            {{ @$parcel->sender_first_name . ' ' . @$parcel->sender_last_name }}
                                        </p>
                                        <p style="margin-bottom: 0px!important">
                                            {{ @$parcel->sender_company_name }}
                                        </p>
                                        <p style="margin-bottom: 0px!important">{{ @$parcel->sender_phone }}</p>
                                        <p style="margin-bottom: 0px!important">{{ @$parcel->pickup_address }}</p>
                                    </td>
                                    <td>
                                        <p style="margin-bottom: 0px!important">
                                            {{ @$parcel->customer_first_name . ' ' . @$parcel->customer_last_name }}
                                        </p>
                                        <p style="margin-bottom: 0px!important"> {{ @$parcel->customer_company_name }}</p>
                                        <p style="margin-bottom: 0px!important"> {{ @$parcel->customer_phone }}</p>
                                        <p style="margin-bottom: 0px!important">{{ @$parcel->customer_address }}</p>
                                    </td>
                                    <td width="15%">
                                        <div class="border-box">
                                            <div class="border-dotted"></div>
                                            <div class="d-flex"><i class="fa-regular fa-circle me-2 fs-7 text-primary"></i>
                                                <div>{{ $parcel->pickup_location }}</div>
                                            </div><br />
                                            <div class="mt-2 d-flex"><i class="fa fa-location-dot me-2 fs-6 text-primary"></i>
                                                <div>{{ $parcel->drop_location }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td width="15%">
                                        @php
                                            $whoPaysText = (int) $parcel->who_pays === \App\Enums\WhoPays::RECIPIENT ? 'Receiver' : 'Sender';
                                            $paymentStatusText = 'Unpaid';
                                            if ($parcel->invoice && (int) $parcel->invoice->status === \App\Enums\InvoiceStatus::PAID) {
                                                $paymentStatusText = 'Paid';
                                            } elseif ((int) $parcel->payment_status === \App\Enums\InvoiceStatus::PAID) {
                                                $paymentStatusText = 'Paid';
                                            } elseif ((int) $parcel->payment_status === \App\Enums\InvoiceStatus::PROCESSING) {
                                                $paymentStatusText = 'Processing';
                                            }
                                        @endphp
                                        <div><b>Who Pays:</b> {{ $whoPaysText }}</div>
                                        <div><b>Payment:</b> {{ $paymentStatusText }}</div>
                                        <div><b>{{ __('parcel.distance') }}:</b> {{ number_format((float) $parcel->distance_km, 2) }} km</div>
                                        <div><b>{{ __('levels.total_delivery_amount') }}:</b> {{ settings()->currency }} {{ number_format((float) $parcel->total_delivery_amount, 2) }}</div>
                                        <div><b>Final Paid:</b> {{ settings()->currency }} {{ number_format((float) ($parcel->final_paid_amount ?? 0), 2) }}</div>
                                    </td>
                                    <td>
                                        <div class="mb-2">
                                            {!! @$parcel->isSmallBatch !!}<br />
                                        </div>
                                        <div class="mb-2">
                                            {!! $parcel->parcel_status !!}
                                        </div>
                                        <span>{{ __('parcel.updated_on') }}: {{ \Carbon\Carbon::parse($parcel->updated_at)->format('Y-m-d h:i A') }}</span>
                                    </td>
                                    @if (hasPermission('parcel_status_update') == true)
                                    <td>
                                        @if (
                                            !in_array($parcel->status, [
                                                \App\Enums\ParcelStatus::DELIVERED,
                                                \App\Enums\ParcelStatus::PARTIAL_DELIVERED,
                                                \App\Enums\ParcelStatus::RETURN_RECEIVED_BY_MERCHANT,
                                                \App\Enums\ParcelStatus::MARKETPLACE_DELIVERED,
                                                \App\Enums\ParcelStatus::MARKETPLACE_CANCELLED,
                                            ])
                                        )

                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend be-addon">
                                                <button tabindex="-1" data-toggle="dropdown"
                                                    type="button"
                                                    class="btn btn-primary dropdown-toggle dropdown-toggle-split"><span
                                                        class="sr-only">Toggle Dropdown</span></button>
                                                <div class="dropdown-menu">
                                                    {!! parcelStatus($parcel) !!}
                                                </div>
                                            </div>
                                        </div>
                                        @else
                                        <span style="font-size: 20px;">...</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if (in_array($parcel->status, [\App\Enums\ParcelStatus::DELIVERED, \App\Enums\ParcelStatus::MARKETPLACE_DELIVERED]))
                                        <a href="{{ route('parcel.deliveredInfo', $parcel->id) }}"
                                            class="btn btn-sm btn-warning ml-1 " data-toggle="tooltip"
                                            data-placement="top" title="View">{{ __('View Proof') }}</a>
                                        @endif

                                        @if ($parcel->invoice)
                                        <p class="mb-0">{{ __('invoice.'.@$parcel->invoice->status) }}</p>
                                        {{ @$parcel->invoice->invoice_id }}<br />
                                        @if ($parcel->invoice->status == App\Enums\InvoiceStatus::PAID)
                                        Paid At: {{ @dateFormat(@$parcel->invoice->updated_at) }}
                                        @endif
                                        @else
                                        N/A
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @include('backend.parcel.cancel_modal')
                        @include('backend.parcel.all_action_modal')
                        @include('backend.parcel.pickup_assign_modal')
                        @include('backend.parcel.pickup_re_schedule')
                        @include('backend.parcel.received_by_pickup')
                        @include('backend.parcel.delivery_man_assign')
                        @include('backend.parcel.delivery_reschedule')
                        @include('backend.parcel.partial_delivered_modal')
                        @include('backend.parcel.delivered_modal')
                        @include('backend.parcel.received_warehouse')
                        @include('backend.parcel.return_to_qourier')
                        @include('backend.parcel.failure_modal')
                        @include('backend.parcel.cancelled_modal')
                        @include('backend.parcel.return_assign_to_merchant')
                        @include('backend.parcel.re_schedule_return_assign_to_merchant')
                        @include('backend.parcel.return_received_by_merchant')
                        @include('backend.parcel.unconfirmed_modal')
                        @include('backend.parcel.heading_to_pickup_point_modal')
                        @include('backend.parcel.picked_up_modal')
                        @include('backend.parcel.heading_to_drop_off_branch_modal')
                        @include('backend.parcel.heading_to_delivery_point_modal')
                        @include('backend.parcel.confirmed_modal')
                        @include('backend.parcel.immediate_execution_modal')
                        @include('backend.parcel.transit_out_city_modal')
                        @include('backend.parcel.on_the_way_to_city_modal')
                        @include('backend.parcel.arrived_at_city_modal')
                        @include('backend.parcel.dropped_of_city_modal')
                        @include('backend.parcel.assign_pickup_bulk')
                        @include('backend.parcel.delivery_man_assign_multiple_parcel')
                        @include('backend.parcel.assign_return_to_merchant_bulk')
                    </div>
                </div>
                <div class="pb-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="table-responsive">
                        <span>{{ $parcels->appends($request->all())->links() }}</span>
                        <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
                            {!! __('Showing') !!}
                            <span class="font-medium">{{ $parcels->firstItem() }}</span>
                            {!! __('to') !!}
                            <span class="font-medium">{{ $parcels->lastItem() }}</span>
                            {!! __('of') !!}
                            <span class="font-medium">{{ $parcels->total() }}</span>
                            {!! __('results') !!}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <!-- end data table  -->
    </div>
</div>
<!-- end wrapper  -->
@endsection()

<!-- css  -->
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<style>
    #selectAssignType .select2-container .select2-selection--single {
        height: 32px !important;
    }
</style>
@endpush
<!-- js  -->
@push('scripts')
<script>
    var merchantUrl = '{{ route('parcel.merchant.get') }}';
    var merchantID = '{{ $request->parcel_merchant_id }}';
    var dateParcel = '{{ $request->parcel_date }}';
</script>
<script src="{{ static_asset('js/onscan.js/onscan.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="{{ static_asset('backend/js/date-range-picker/date-range-picker-custom.js') }}"></script>
<script src="{{ static_asset('backend/js/parcel/custom.js') }}"></script>
<script src="{{ static_asset('backend/js/parcel/filter.js') }}"></script>
<script>
    @if(!empty($errors->all()))
    @foreach($errors->all() as $error)
    toastr.error("{{$error}}")
    @endforeach
    @endif
</script>
@endpush