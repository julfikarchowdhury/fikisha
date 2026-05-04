@extends('backend.partials.master')
@section('title')
{{ __('parcel.title') }} {{ __('levels.list') }}
@endsection
@section('maincontent')
<!-- wrapper  -->
<div class="container-fluid  dashboard-content">
    <!-- pageheader -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="page-header">
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}"
                                    class="breadcrumb-link">{{ __('levels.dashboard') }}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('merchant-panel.parcel.index') }}"
                                    class="breadcrumb-link">{{ __('parcel.title') }}</a>
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
    <!-- end pageheader -->



    <div class="row">
        <!-- data table  -->
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('merchant-panel.parcel.filter') }}" method="GET">
                        <div class="row">
                            <div class="form-group col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2">
                                <label for="parcel_date">{{ __('parcel.date') }}</label>
                                <input type="text" autocomplete="off" id="date" name="parcel_date"
                                    placeholder="Enter Date" class="form-control date_range_picker"
                                    value="{{ old('parcel_date', $request->parcel_date) }}">
                                @error('parcel_date')
                                <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2">
                                <label for="parcelStatus">{{ __('parcel.status') }}</label>
                                <select style="width: 100%" id="parcelStatus"
                                    name="parcel_status" class="form-control @error('parcel_status') is-invalid @enderror">
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
                                    <option value="{{ $key }}"
                                        {{ old('parcel_status', $request->parcel_status) == $key ? 'selected' : '' }}>
                                        {{ trans('parcelStatusShow.' . $key) }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('parcel_status')
                                <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>


                            <div class="form-group col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2">
                                <label for="payment_status">{{ __('parcel.payment') }}</label>
                                <select id="payment_status" name="payment_status" class="form-control select2">
                                    <option value="">{{ __('levels.select') }} {{ __('parcel.payment') }}
                                    </option>
                                    <option value="{{ App\Enums\InvoiceStatus::UNPAID }}" @selected($request->payment_status == App\Enums\InvoiceStatus::UNPAID)>
                                        {{ __('Unpaid') }}
                                    </option>
                                    <option value="{{ App\Enums\InvoiceStatus::PROCESSING }}"
                                        @selected($request->payment_status == App\Enums\InvoiceStatus::PROCESSING)>{{ __('Processing') }}</option>
                                    <option value="{{ App\Enums\InvoiceStatus::PAID }}" @selected($request->payment_status == App\Enums\InvoiceStatus::PAID)>
                                        {{ __('Paid') }}
                                    </option>
                                </select>
                            </div>

                            <div class="form-group col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2 pt-1 pl-0">
                                <div class="col-12 pt-4 d-flex justify-content text-right">
                                    <button type="submit" class="btn btn-sm btn-space btn-primary">
                                        <i class="fa fa-filter"></i> {{ __('levels.filter') }}
                                    </button>
                                    <a href="{{ route('merchant-panel.parcel.index') }}"
                                        class="btn btn-sm btn-space btn-secondary">
                                        <i class="fa fa-eraser"></i> {{ __('levels.clear') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="row pl-4 pr-4 pt-4 merchantParcelPage">
                    <div class="col-8">
                        <div class="d-flex   parcelsearchFlex parcel-import-export-btn">
                            <p class="h3 mr-5">{{ __('parcel.title') }} </p>
                            <div class="d-flex justify-content-start mt-md-0 d-lg-block   ">
                                <a href="{{ route('merchant-panel.parcel.file-export', ['parcel_date' => $request->parcel_date, 'parcel_status' => $request->parcel_status, 'parcel_customer' => $request->parcel_customer, 'parcel_customer_phone' => $request->parcel_customer_phone]) }}"
                                    class="btn btn-success btn-sm " data-toggle="tooltip" data-placement="top"
                                    title="Add"> <i class="fa fa-download"></i> {{ __('parcel.export_xlsx') }}</a>
                                <a href="{{ route('merchant-panel.parcel.file-export', ['parcel_date' => $request->parcel_date, 'parcel_status' => $request->parcel_status, 'parcel_customer' => $request->parcel_customer, 'parcel_customer_phone' => $request->parcel_customer_phone, 'type' => 'csv']) }}"
                                    class="btn btn-success btn-sm " data-toggle="tooltip" data-placement="top"
                                    title="Add"> <i class="fa fa-download"></i> {{ __('parcel.export_csv') }}</a>

                            </div>
                        </div>
                    </div>
                    <div class="col-4 ">
                        <div class="d-flex justify-content-end  mt-0 parcel-create-import-btn-start">
                            {{-- multiple parcel label print --}}
                            <form action="{{ route('parcel.multiple.print-label') }}" method="get" target="_blank"
                                id="print_label_form">
                                @csrf
                                <div id="print_label_content"></div>
                                <button type="submit" class="btn btn-sm btn-primary mr-2 multiplelabelprint"
                                    data-parcels='' style="display: none">{{ __('levels.print_label') }}</button>
                            </form>
                            {{-- end multiple parcel label print --}}
                            <a href="{{ route('merchant-panel.parcel.create', ['fresh' => 1]) }}" class="btn btn-primary btn-sm mr-2"
                                data-toggle="tooltip" data-placement="top" title="Add">
                                <i class="fa fa-plus"></i> {{ __('levels.new_order') }}
                            </a>
                        </div>
                    </div>
                    <div class="col-12 d-lg-none mt-2 ">
                        <div class="d-flex justify-content-end mt-md-0   ">
                            <a href="{{ route('merchant-panel.parcel.file-export', ['parcel_date' => $request->parcel_date, 'parcel_status' => $request->parcel_status, 'parcel_customer' => $request->parcel_customer, 'parcel_customer_phone' => $request->parcel_customer_phone]) }}"
                                class="btn btn-success btn-sm " data-toggle="tooltip" data-placement="top"
                                title="Add"> <i class="fa fa-download"></i> {{ __('parcel.export_xlsx') }}</a>
                            <a href="{{ route('merchant-panel.parcel.file-export', ['parcel_date' => $request->parcel_date, 'parcel_status' => $request->parcel_status, 'parcel_customer' => $request->parcel_customer, 'parcel_customer_phone' => $request->parcel_customer_phone, 'type' => 'csv']) }}"
                                class="btn btn-success btn-sm " data-toggle="tooltip" data-placement="top"
                                title="Add"> <i class="fa fa-download"></i> {{ __('parcel.export_csv') }}</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-pills mb-3 parcel-status-btn" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="mt-2 nav-link btn btn-active @if (!$request->parcel_status) active @endif me-2"
                                href="{{ route('merchant-panel.parcel.index') }}">{{ __('parcel.all') }} <span
                                    class="badge badge-primary">
                                    {{ \App\Models\Backend\Parcel::where('merchant_id', Auth::user()->merchant->id)->count() }}
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
                                    href="{{ route('merchant-panel.parcel.filter', ['parcel_status' => $key]) }}">
                                    {{ trans('parcelStatusShow.' . $key) }}
                                    <span class="badge badge-primary">
                                        {{ \App\Models\Backend\Parcel::where('merchant_id', Auth::user()->merchant->id)->where('status', $key)->count() }}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="table-responsive">
                        <table id="table" class="table   " style="width:100%">
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
                                    <th>{{ __('parcel.payment') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1; @endphp
                                @foreach ($parcels as $parcel)
                                <tr>
                                    <td class="parcel-index permission-check-box">
                                        <input type="checkbox" name="parcels[][{{ $parcel->id }}]"
                                            value="{{ $parcel->id }}" class="common-key form-check-input" />
                                    </td>
                                    <td>
                                        <div class="row">
                                            <button tabindex="-1" data-toggle="dropdown" type="button"
                                                class="btn btn-sm ml-2 bnone"><i class="fa fa-cogs"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a href="{{ route('merchant-panel.parcel.details', $parcel->id) }}"
                                                    class="dropdown-item">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>
                                                    {{ __('levels.view') }}
                                                </a>
                                                <a href="{{ route('merchant-panel.parcel.logs', $parcel->id) }}"
                                                    class="dropdown-item">
                                                    <i class="fas fa-history" aria-hidden="true"></i>
                                                    {{ __('levels.parcel_logs') }}
                                                </a>
                                                <!-- <a href="{{ route('merchant-panel.parcel.placed', $parcel->id) }}"
                                                    class="dropdown-item">
                                                    <i class="fas fa-box" aria-hidden="true"></i>
                                                    {{ __('levels.order_placed') }}
                                                </a>
                                                <a href="{{ route('merchant-panel.parcel.print', $parcel->id) }}"
                                                    target="_blank" class="dropdown-item">
                                                    <i class="fas fa-print" aria-hidden="true"></i>
                                                    {{ __('levels.print') }}
                                                </a>
                                                <a href="{{ route('merchant-parcel.clone', $parcel->id) }}"
                                                    class="dropdown-item">
                                                    <i class="fas fa-clone" aria-hidden="true"></i>
                                                    {{ __('levels.clone') }}
                                                </a> -->
                                                @if (in_array($parcel->status, [
                                                    App\Enums\ParcelStatus::MARKETPLACE_ACCEPTED,
                                                    App\Enums\ParcelStatus::MARKETPLACE_PICKED_UP,
                                                    App\Enums\ParcelStatus::MARKETPLACE_DELIVERED,
                                                ], true))
                                                <a href="{{ route('merchant-panel.parcel.dispute.create', $parcel->id) }}"
                                                    class="dropdown-item">
                                                    <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
                                                    Raise Dispute
                                                </a>
                                                @endif
                                                @if (
                                                    in_array((int) $parcel->status, [
                                                        \App\Enums\ParcelStatus::MARKETPLACE_SENDER_PAYMENT_AWAITING,
                                                        \App\Enums\ParcelStatus::MARKETPLACE_RECEIVER_PAYMENT_AWAITING,
                                                    ], true) &&
                                                    (int) $parcel->payment_status !== \App\Enums\InvoiceStatus::PAID
                                                )
                                                    <a href="{{ route('merchant-panel.mpesa.pay.parcel.form', $parcel->id) }}"
                                                        class="dropdown-item">
                                                        <i class="fa fa-mobile" aria-hidden="true"></i>
                                                        Complete Payment
                                                    </a>
                                                @endif
                                                @if (!in_array($parcel->status, [\App\Enums\ParcelStatus::MARKETPLACE_DELIVERED, \App\Enums\ParcelStatus::MARKETPLACE_CANCELLED, \App\Enums\ParcelStatus::DELIVERED], true))
                                                @if (in_array($parcel->status, [App\Enums\ParcelStatus::PENDING, App\Enums\ParcelStatus::MARKETPLACE_PENDING], true))
                                                <a href="{{ route('merchant-panel.parcel.edit', $parcel->id) }}"
                                                    class="dropdown-item"><i class="fas fa-edit"
                                                        aria-hidden="true"></i>
                                                    {{ __('levels.edit') }}</a>
                                                <form id="delete" value="Test"
                                                    action="{{ route('merchant-panel.parcel.delete', $parcel->id) }}"
                                                    method="POST"
                                                    data-title="{{ __('delete.parcel') }}">
                                                    @method('DELETE')
                                                    @csrf
                                                    <input type="hidden" name="" value="Parcel"
                                                        id="deleteTitle">
                                                    <button type="submit" class="dropdown-item"><i
                                                            class="fa fa-trash" aria-hidden="true"></i>
                                                        {{ __('levels.delete') }}</button>
                                                </form>
                                                @endif
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        #{{ $parcel->tracking_id }}<br />
                                        @if(!empty($parcel->tracking_token))
                                            <a href="{{ route('track.public', $parcel->tracking_token) }}"
                                                class="btn btn-outline-primary mt-2"
                                                target="_blank">{{ __('parcel.tracking') }} <i
                                                    class="fa-solid fa-arrow-up-right-from-square"></i></a>
                                        @else
                                            <a href="{{ route('merchant-panel.parcel.logs', $parcel->id) }}"
                                                class="btn btn-outline-primary mt-2"
                                                target="_blank">{{ __('parcel.tracking') }} <i
                                                    class="fa-solid fa-arrow-up-right-from-square"></i></a>
                                        @endif
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
                                        <p style="margin-bottom: 0px!important">
                                            {{ @$parcel->customer_company_name }}
                                        </p>
                                        <p style="margin-bottom: 0px!important"> {{ @$parcel->customer_phone }}
                                        </p>
                                        <p style="margin-bottom: 0px!important">{{ @$parcel->customer_address }}</p>
                                    </td>
                                    <td width="15%">
                                        <div class="border-box">
                                            <div class="border-dotted"></div>
                                            <div class="d-flex"><i
                                                    class="fa-regular fa-circle me-2 fs-7 text-primary"></i>
                                                <div>{{ $parcel->pickup_location }}</div>
                                            </div><br />
                                            <div class="mt-2 d-flex"><i
                                                    class="fa fa-location-dot me-2 fs-6 text-primary"></i>
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
                                        <p class="mb-2">{!! @$parcel->isSmallBatch !!}</p>
                                        <p class="mb-2">{!! $parcel->parcel_status !!}</p>
                                        @php
                                            $riderUser = null;
                                            if (!empty($parcel->delivery_man_id)) {
                                                $riderUser = \App\Models\User::find($parcel->delivery_man_id);
                                            }
                                        @endphp
                                        @if ($riderUser)
                                            <div class="small"><b>Rider:</b> {{ $riderUser->name }}</div>
                                            <div class="small text-muted">{{ $riderUser->mobile ?? '-' }}</div>
                                        @elseif (in_array((int) $parcel->status, [
                                            \App\Enums\ParcelStatus::MARKETPLACE_ACCEPTED,
                                            \App\Enums\ParcelStatus::MARKETPLACE_PICKED_UP,
                                            \App\Enums\ParcelStatus::MARKETPLACE_DELIVERED,
                                        ], true))
                                            <div class="small text-muted">Rider assigning...</div>
                                        @endif
                                        <span>{{ __('parcel.updated_on') }}:<br />
                                            {{ \Carbon\Carbon::parse($parcel->updated_at)->format('Y-m-d h:i A') }}</span>
                                    </td>
                                    <td>
                                        @if ($parcel->invoice)
                                        <p class="mb-0">{{ __('invoice.' . @$parcel->invoice->status) }}</p>
                                        {{ @$parcel->invoice->invoice_id }}<br />
                                        @if ($parcel->invoice->status == App\Enums\InvoiceStatus::PAID)
                                        Paid At: {{ @dateFormat(@$parcel->invoice->updated_at) }}
                                        @endif
                                        @else
                                        N/A
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="pb-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
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
        <!-- end data table  -->
    </div>
</div>
<!-- end wrapper  -->
@endsection()
<!-- css  -->
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush
<!-- js  -->
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="{{ static_asset('backend/js/date-range-picker/date-range-picker-custom.js') }}">
</script>
<script>
    var merchantUrl = '{{ route('merchant-panel.parcel.merchant.get') }}';
    var dateParcel = '{{ $request->parcel_date }}';
</script>
<script src="{{ static_asset('backend/js/merchant_panel/parcel/filter.js') }}"></script>
<script src="{{ static_asset('backend/js/parcel/parcel-search.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        //multiple parcel label print
        $('#tick-all').on('change', function() {
            if (!$(this).is(':checked')) {
                $('td').closest('tr').find('.common-key').prop('checked', false)
            } else {
                if ($(this).is(':checked')) {
                    $('td').closest('tr').find('.common-key').prop('checked', true)
                }
            }
            showPrintBtn();
        });

        $('.common-key').on('click', function() {
            showPrintBtn();
        })

        function showPrintBtn() {
            if ($('.common-key:checked').length > 0) {
                $('.multiplelabelprint').show();
                var inputs = '';
                $('.common-key:checked').each(function() {
                    inputs += '<input type="hidden" name="parcels[]" value="' + $(this).val() + '"/>';
                });
                $('#print_label_content').html(inputs);
            } else {
                $('.multiplelabelprint').hide();
                $('#tick-all').prop('checked', false);
                $('#print_label_content').html('');
            }
        }

        $("#hub_id").on("change", function() {
            var hub_id = $(this).val();
            $("#hidden_hub_id").val(hub_id);
        });

        $("#from_province_id").on("change", function() {
            var url = $(this).data("url");
            var id = $(this).val();
            $("#from_city_id").html("");
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    id
                },
                success: function(data) {
                    var cityOptions = "";
                    if (data && data.city_option) {
                        cityOptions = data.city_option;
                    } else if (Array.isArray(data)) {
                        cityOptions = '<option value="">---Select City---</option>';
                        data.forEach(function(city) {
                            cityOptions += '<option value="' + city.id + '">' + city.name + "</option>";
                        });
                    }
                    $("#from_city_id").html(cityOptions);
                },
            });
        });

        $("#to_province_id").on("change", function() {
            var url = $(this).data("url");
            var id = $(this).val();
            $("#to_city_id").val("");
            $.ajax({
                type: "POST",
                url: url,
                data: {
                    id
                },
                success: function(data) {
                    var cityOptions = "";
                    if (data && data.city_option) {
                        cityOptions = data.city_option;
                    } else if (Array.isArray(data)) {
                        cityOptions = '<option value="">---Select City---</option>';
                        data.forEach(function(city) {
                            cityOptions += '<option value="' + city.id + '">' + city.name + "</option>";
                        });
                    }
                    $("#to_city_id").html(cityOptions);
                },
            });
        });

        $(document).on('change', '#delivery_type_id', function() {
            var url = $(this).data('url');
            var delivery_type_id = $(this).val();
            $.ajax({
                type: 'get',
                url: url,
                data: {
                    'delivery_type_id': delivery_type_id
                },
                dataType: "html",
                success: function(data) {
                    $('#shipping_type').html(data);
                }
            });

            if (delivery_type_id == 1) {
                $(".fromProvince").removeClass('d-none');
                $(".fromProvince").addClass('d-block');
                $(".toProvince").removeClass('d-block');
                $(".toProvince").addClass('d-none');
                $(".from_province_label").text('Province');
            } else if (delivery_type_id == 3) {
                $(".toProvince").removeClass('d-none');
                $(".toProvince").addClass('d-block');
                $(".fromProvince").removeClass('d-none');
                $(".fromProvince").addClass('d-block');
                $(".from_province_label").text('From Province');
            }
            $("#from_province_id").val("").change();
            $("#to_province_id").val("").change();
        });
    });
</script>
@endpush