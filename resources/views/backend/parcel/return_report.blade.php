@extends('backend.partials.master')
@section('title')
{{ __('parcel.return_report') }} {{ __('levels.list') }}
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
                    <form method="GET">
                        <div class="row">
                            <div class="form-group col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2">
                                <label for="parcel_date">{{ __('parcel.date') }}</label>
                                <input type="text" autocomplete="off" id="date" name="parcel_date" placeholder="Enter Date" class="form-control date_range_picker" value="{{ old('parcel_date', $request->parcel_date) }}">
                                @error('parcel_date')
                                <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group col-12 col-md-3 col-sm-6  col-lg-3 col-xl-2">
                                <label for="delivery_type_id">{{ __('parcel.delivery_type') }}</label>
                                <select style="width: 100%" id="delivery_type_id" data-url="{{ route('get.shipping.types') }}" name="delivery_type_id" class="form-control select2 @error('delivery_type_id') is-invalid @enderror">
                                    <option value=""> {{ __('menus.select') }} {{ __('parcel.delivery_type') }}</option>
                                    @foreach (\App\Models\Backend\DeliveryType::active()->orderBy('position')->get() as $key => $deliverytype)
                                    <option value="{{ $deliverytype->id }}" @selected(old('delivery_type_id', $request->delivery_type_id) == $deliverytype->id)>
                                        {{ $deliverytype->title }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('delivery_type_id')
                                <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2 fromProvince {{ $request->delivery_type_id == 1 || $request->delivery_type_id == 3 ? 'd-block' : 'd-none' }}">
                                <label for="from_province_id" class="from_province_label">{{ $request->delivery_type_id && $request->delivery_type_id == 1 ? __('parcel.province') : __('parcel.from_province') }}</label>
                                <select id="from_province_id" name="from_province_id" class="form-control select2" style="width: 100%;">
                                    <option value="">{{ __('levels.select') }} {{ __('parcel.province') }}</option>
                                    @foreach (\App\Models\Backend\Province::all() as $province)
                                    <option value="{{ $province->id }}" @selected($request->from_province_id == $province->id)>
                                        {{ $province->name }}({{ $province->province_code }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2 toProvince {{ $request->delivery_type_id == 3 ? 'd-block' : 'd-none' }}">
                                <label for="to_province_id">{{ __('parcel.to_province') }}</label>
                                <select id="to_province_id" name="to_province_id" class="form-control select2" style="width: 100%;">
                                    <option value="">{{ __('levels.select') }} {{ __('parcel.province') }}</option>
                                    @foreach (\App\Models\Backend\Province::all() as $province)
                                    <option value="{{ $province->id }}" @selected($request->to_province_id == $province->id)>
                                        {{ $province->name }}({{ $province->province_code }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-12 col-md-3 col-sm-6  col-lg-3 col-xl-2">
                                <label for="shipping_type">{{ __('parcel.shipping_type') }}</label>
                                <select style="width: 100%" id="shipping_type" name="shipping_type" class="form-control select2 @error('shipping_type') is-invalid @enderror">
                                    <option value=""> {{ __('menus.select') }} {{ __('parcel.shipping_type') }}</option>
                                    @foreach ($shippingTypes as $shipping_type)
                                    @if ($request->delivery_type_id == $shipping_type->delivery_type_id)
                                    <option value="{{ $shipping_type->id }}" @selected(old('shipping_type', $request->shipping_type) == $shipping_type->id)>
                                        {{ $shipping_type->title }}
                                    </option>
                                    @endif
                                    @endforeach
                                </select>
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
                            <div class="form-group col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2 pt-1 pl-0">
                                <div class="col-12 pt-4 d-flex justify-content text-right">
                                    <button type="submit" class="btn btn-sm btn-space btn-primary">
                                        <i class="fa fa-filter"></i> {{ __('levels.filter') }}
                                    </button>
                                    <a href="{{ route('parcel.return_report') }}" class="btn btn-sm btn-space btn-secondary">
                                        <i class="fa fa-eraser"></i> {{ __('levels.clear') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header flex-column d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <form method="get">
                        <div class="d-flex parcelsearchFlex">
                            <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('parcel.return_report') }}</h4>
                        </div>
                    </form>
                    <div class="d-lg-none">
                        <form method="get">
                            <div class="d-flex parcelsearchFlex ml-0">
                                <input id="Psearch" class="parcelml-0 form-control group-input w-100" name="search" type="text" placeholder="{{ __('levels.search') }}..." value="{{ $request->search }}">
                                <button type="submit" class="btn btn-sm btn-space btn-primary group-btn" style="margin-bottom: 0px;margin-left:0px!important"><i class="fa fa-filter"></i>
                                    {{ __('levels.search') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table" class="table parcelTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>SL No</th>
                                    <th>{{ __('###') }}</th>
                                    <th>{{ __('parcel.tracking_id') }}</th>
                                    <th>{{ __('parcel.merchant') }}</th>
                                    <th>{{ __('parcel.recipient_info') }}</th>
                                    <th>{{ __('parcel.location') }}</th>
                                    <th>{{ __('parcel.return_location') }}</th>
                                    <th>{{ __('levels.details') }}</th>
                                    <th>{{ __('parcel.status') }}</th>
                                    <th>{{ __('parcel.payment') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $i = 1;
                                @endphp
                                @foreach ($parcels as $index => $parcel)
                                <tr>
                                    <td><strong>{{ $index + $parcels->firstItem() }}</strong></td>
                                    <td>
                                        <div class="row">
                                            <button tabindex="-1" data-toggle="dropdown" type="button" class="btn btn-sm ml-2 bnone">
                                                <i class="fa fa-cogs"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a href="{{ route('parcel.details', $parcel->id) }}" target="_blank" class="dropdown-item">
                                                    <i class="fa fa-eye" aria-hidden="true"></i> {{ __('levels.view') }}
                                                </a>
                                                <a href="{{ route('parcel.logs', $parcel->id) }}" target="_blank" class="dropdown-item">
                                                    <i class="fas fa-history" aria-hidden="true"></i> {{ __('levels.parcel_logs') }}
                                                </a>
                                                <a href="{{ route('parcel.placed', $parcel->id) }}" target="_blank" class="dropdown-item">
                                                    <i class="fas fa-box" aria-hidden="true"></i> {{ __('levels.order_placed') }}
                                                </a>
                                                <a href="{{ route('return.parcel.print', $parcel->id) }}" target="_blank" class="dropdown-item">
                                                    <i class="fas fa-print" aria-hidden="true"></i> {{ __('levels.print') }}
                                                </a>
                                                <a href="{{ route('parcel.print-label', $parcel->id) }}" target="_blank" class="dropdown-item">
                                                    <i class="fas fa-print" aria-hidden="true"></i> {{ __('levels.print_label') }}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>#{{ $parcel->tracking_id }}</td>
                                    <td>
                                        <p style="margin-bottom: 0px!important">
                                            {{ @$parcel->sender_first_name . ' ' . @$parcel->sender_last_name }}
                                        </p>
                                        <p style="margin-bottom: 0px!important">
                                            {{ @$parcel->sender_company_name }}
                                        </p>
                                        <p style="margin-bottom: 0px!important">{{ @$parcel->sender_email }}</p>
                                        <p style="margin-bottom: 0px!important">{{ @$parcel->sender_phone }}</p>
                                        <p style="margin-bottom: 0px!important">Address: {{ @$parcel->pickup_address }}</p>
                                        <p style="margin-bottom: 0px!important">Province: {{ @$parcel->fromProvince->name }}({{ @$parcel->fromProvince->province_code }})</p>
                                        @if ($parcel->from_point_type == 1)
                                        <p style="margin-bottom: 0px!important">City: {{ @$parcel->fromCity->name }}</p>
                                        <p style="margin-bottom: 0px!important">Portal Code: {{ @$parcel->from_portal_code }}</p>
                                        @endif
                                    </td>
                                    <td>
                                        <p style="margin-bottom: 0px!important">
                                            {{ @$parcel->customer_first_name . ' ' . @$parcel->customer_last_name }}
                                        </p>
                                        <p style="margin-bottom: 0px!important"> {{ @$parcel->customer_company_name }}</p>
                                        <p style="margin-bottom: 0px!important"> {{ @$parcel->receiver_email }}</p>
                                        <p style="margin-bottom: 0px!important"> {{ @$parcel->customer_phone }}</p>
                                        <p style="margin-bottom: 0px!important">Address: {{ @$parcel->customer_address }}</p>
                                        <p style="margin-bottom: 0px!important">Province: {{ @$parcel->toProvince->name }}({{ @$parcel->toProvince->province_code }})</p>
                                        @if ($parcel->to_point_type == 1)
                                        <p style="margin-bottom: 0px!important">City: {{ @$parcel->toCity->name }}</p>
                                        <p style="margin-bottom: 0px!important">Portal Code: {{ @$parcel->to_portal_code }}</p>
                                        @endif
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
                                        <div class="border-box">
                                            <div class="border-dotted"></div>
                                            <div class="d-flex"><i class="fa-regular fa-circle me-2 fs-7 text-primary"></i>
                                                <div>{{ $parcel->drop_location }}</div>
                                            </div><br />
                                            <div class="mt-2 d-flex"><i class="fa fa-location-dot me-2 fs-6 text-primary"></i>
                                                <div>{{ $parcel->pickup_location }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td width="15%">
                                        <b>{{ __('parcel.shipping_type') }} :<br /></b>
                                        {{ @$parcel->ShippingType->title }} <br />
                                        @if ($parcel->pickup_date)
                                        {{ __('parcel.pickup_date') }}: <br />
                                        <span class="text-dark">{{ dateFormatDone($parcel->pickup_date) }}</span><br>
                                        @else
                                        {{ __('parcel.pickup_date') }}: <br />
                                        <span class="text-dark">{{ dateFormatDone($parcel->created_at) }}</span><br>
                                        @endif
                                        <b>{{ __('parcel.delivery_type') }} :<br /></b>
                                        {{ @$parcel->DelivType->title }}<br />
                                        <b>{{ __('levels.slots') }}</b> {{ @$parcel->slots }}<br />
                                        <b>{{ __('parcel.amount') }} {{ __('levels.details') }} </b> <br />
                                        <div class="w250">
                                            {{ __('levels.total_delivery_amount') }}: <span
                                                class="text-dark">{{ settings()->currency }}{{ $parcel->total_delivery_amount }}</span>
                                            <br>
                                            {{ __('parcel.discount') }} (-): <span
                                                class="text-dark">{{ settings()->currency }}{{ $parcel->discount_amount }}</span>
                                            <br>
                                            {{ __('levels.vat_amount') }}: <span
                                                class="text-dark">{{ settings()->currency }}{{ $parcel->vat_amount }}</span>
                                            <br>
                                            {{ __('parcel.total_shipping_fee') }}: <span
                                                class="text-dark">{{ settings()->currency }}{{ $parcel->total_delivery_amount + $parcel->vat_amount - $parcel->discount_amount }}</span>
                                            <br>
                                            {{ __('parcel.parcel_value') }}: <span
                                                class="text-dark">{{ settings()->currency }}{{ $parcel->total_parcel_value }}</span>
                                            <br>
                                            {{ __('levels.current_payable') }}:
                                            <b>{{ settings()->currency }}{{ $parcel->current_payable }}</b>
                                        </div>
                                        @if ($parcel->return_charges)
                                        <div class="w250" style="font-weight: bold;">
                                            {{ __('levels.return_charge') }}:
                                            <b>{{ settings()->currency }}{{ $parcel->return_charges }}</b>
                                        </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="mb-2">
                                            {!! @$parcel->isSmallBatch !!}<br />
                                        </div>
                                        {!! $parcel->parcel_status !!}<br />
                                        <br>
                                        @if ($parcel->partial_delivered && $parcel->status != \App\Enums\ParcelStatus::PARTIAL_DELIVERED)
                                        <span
                                            class="badge badge-pill badge-success mt-2">{{ trans('parcelStatus.' . \App\Enums\ParcelStatus::PARTIAL_DELIVERED) }}</span>
                                        <br />
                                        @endif
                                        <span>{{ __('parcel.updated_on') }}: {{ \Carbon\Carbon::parse($parcel->updated_at)->format('Y-m-d h:i A') }}</span>
                                    </td>
                                    <td>
                                        @php
                                        if ($parcel->admin_parcel_invoice !== null && $parcel->admin_parcel_invoice->status == App\Enums\InvoiceStatus::PAID):
                                        $status = $parcel->admin_parcel_invoice->status;
                                        elseif ($parcel->admin_parcel_invoice !== null && $parcel->admin_parcel_invoice->status == App\Enums\InvoiceStatus::UNPAID):
                                        $status = App\Enums\InvoiceStatus::UNPAID;
                                        elseif ($parcel->admin_parcel_invoice !== null):
                                        if ($parcel->status == App\Enums\ParcelStatus::DELIVERED || $parcel->status == App\Enums\ParcelStatus::PARTIAL_DELIVERED):
                                        $status = App\Enums\InvoiceStatus::PROCESSING;
                                        else:
                                        $status = App\Enums\InvoiceStatus::UNPAID;
                                        endif;
                                        else:
                                        $status = App\Enums\InvoiceStatus::UNPAID;
                                        endif;
                                        @endphp
                                        <p>{{ __('invoice.' . $status) }}</p>
                                        <span>
                                            {{ @$parcel->admin_parcel_invoice->invoice_id }}<br />
                                            @if ($parcel->admin_parcel_invoice !== null && $parcel->admin_parcel_invoice->status == App\Enums\InvoiceStatus::PAID)
                                            Paid At:
                                            {{ @dateFormat($parcel->admin_parcel_invoice->updated_at) }}
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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
@endpush
<!-- js  -->
@push('scripts')
<script>
    var merchantUrl = '{{ route('parcel.merchant.get') }}';
    var merchantID = '{{ $request->parcel_merchant_id }}';
    var dateParcel = '{{ $request->parcel_date }}';
</script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="{{ static_asset('backend/js/date-range-picker/date-range-picker-custom.js') }}"></script>
<script src="{{ static_asset('backend/js/parcel/filter.js') }}"></script>
<script type="text/javascript">
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
</script>
<script>
    @if(!empty($errors->all()))
    @foreach($errors->all() as $error)
    toastr.error("{{$error}}")
    @endforeach
    @endif
</script>
@endpush