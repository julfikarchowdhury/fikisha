@extends('backend.partials.master')
@section('title')
    {{ __('menus.active_live_monitoring') }} {{ __('levels.list') }}
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
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                        class="breadcrumb-link">{{ __('levels.dashboard') }}</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('merchant-panel.parcel.index') }}"
                                        class="breadcrumb-link">{{ __('menus.active_live_monitoring') }}</a></li>
                                <li class="breadcrumb-item"><a href="#"
                                        class="breadcrumb-link active">{{ __('levels.list') }}</a></li>
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
                        <form action="{{ route('merchant-panel.active.live.monitoring') }}" method="GET">

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
                                    <select id="from_province_id" name="from_province_id" data-url="{{ route('get.province.city') }}" class="form-control select2" style="width: 100%;">
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
                                    <select id="to_province_id" name="to_province_id" data-url="{{ route('get.province.city') }}" class="form-control select2" style="width: 100%;">
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
                                    <label for="parcelStatus">{{ __('parcel.status') }}</label>
                                    <select style="width: 100%" id="parcelStatus" name="parcel_status"class="form-control @error('parcel_status') is-invalid @enderror">
                                        <option value="">All Orders</option>
                                        @foreach (trans('parcelStatusFilter') as $key => $status)
                                            @if ($key ==  App\Enums\ParcelStatus::DELIVERED || $key ==  App\Enums\ParcelStatus::DELIVERY_FAILURE)
                                                @continue
                                            @endif
                                            <option value="{{ $key }}" {{ old('parcel_status', $request->parcel_status) == $key ? 'selected' : '' }}>
                                                {{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('parcel_status')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2">
                                    <label for="sending_hub_id">{{ __('parcel.sending_hub') }}</label>
                                    <select id="sending_hub_id" name="sending_hub_id" class="form-control select2">
                                        <option value="">{{ __('levels.select') }} {{ __('parcel.sending_hub') }}</option>
                                        @foreach (hubs() as $fromHub)
                                            <option value="{{ $fromHub->id }}" @selected($request->sending_hub_id == $fromHub->id)>
                                                {{ $fromHub->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2">
                                    <label for="receiving_hub_id">{{ __('parcel.receiving_hub') }}</label>
                                    <select id="receiving_hub_id" name="receiving_hub_id" class="form-control select2">
                                        <option value="">{{ __('levels.select') }} {{ __('parcel.receiving_hub') }}</option>
                                        @foreach (hubs() as $toHub)
                                            <option value="{{ $toHub->id }}" @selected($request->receiving_hub_id == $toHub->id)>
                                                {{ $toHub->name }}
                                            </option>
                                        @endforeach
                                    </select>
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
                                <div class="form-group col-md-3 col-lg-3 col-xl-2">
                                    <div class="pt-4 d-flex margin-top-5px">
                                        <button type="submit" class="btn btn-space btn-primary"><i
                                                class="fa fa-filter"></i> {{ __('levels.filter') }}</button>
                                        <a href="{{ route('merchant-panel.active.live.monitoring') }}"
                                            class="btn btn-space btn-secondary"><i class="fa fa-eraser"></i>
                                            {{ __('levels.clear') }}</a>
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
                                <p class="h3 mr-5">{{ __('menus.active_live_monitoring') }} </p>
                                <div class="d-flex justify-content-start mt-md-0 d-lg-block   ">
                                    <a href="{{ route('merchant-panel.monitoring.export', ['parcel_date' => $request->parcel_date, 'parcel_status' => $request->parcel_status, 'parcel_customer' => $request->parcel_customer, 'parcel_customer_phone' => $request->parcel_customer_phone, 'monitoring' => 'active_live_monitoring']) }}"
                                        class="btn btn-success btn-sm " data-toggle="tooltip" data-placement="top"
                                        title="Add"> <i class="fa fa-download"></i> {{ __('parcel.export_xlsx') }}</a>
                                    <a href="{{ route('merchant-panel.monitoring.export', ['parcel_date' => $request->parcel_date, 'parcel_status' => $request->parcel_status, 'parcel_customer' => $request->parcel_customer, 'parcel_customer_phone' => $request->parcel_customer_phone, 'monitoring' => 'active_live_monitoring', 'type' => 'csv']) }}"
                                        class="btn btn-success btn-sm " data-toggle="tooltip" data-placement="top"
                                        title="Add"> <i class="fa fa-download"></i> {{ __('parcel.export_csv') }}</a>
                                    <a href="{{ route('merchant-panel.monitoring.export', ['parcel_date' => $request->parcel_date, 'parcel_status' => $request->parcel_status, 'parcel_customer' => $request->parcel_customer, 'parcel_customer_phone' => $request->parcel_customer_phone, 'monitoring' => 'active_live_monitoring', 'type' => 'pdf']) }}"
                                        class="btn btn-success btn-sm mr-2" data-toggle="tooltip" data-placement="top"
                                        title="Add"> <i class="fa fa-download"></i> {{ __('parcel.export_pdf') }}</a>
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
                            @foreach (__('parcelStatusFilter') as $key => $status)
                                @if ($key == \App\Enums\ParcelStatus::PENDING)
                                    <li class="nav-item" role="presentation">
                                        <a class="mt-2 nav-link btn btn-active {{ $request->parcel_status == $key ? 'active' : '' }} me-2"
                                            href="{{ route('merchant-panel.active.live.monitoring', ['parcel_status' => $key]) }}">{{ $status }}
                                            <span class="badge badge-primary">
                                                @php 
                                                    $pendingOrder = \App\Models\Backend\Parcel::wherein('status', [
                                                        App\Enums\ParcelStatus::PENDING,
                                                        App\Enums\ParcelStatus::DELIVERY_MAN_ASSIGN_CANCEL,
                                                        App\Enums\ParcelStatus::DELIVERY_RE_SCHEDULE_CANCEL,
                                                    ])->count();
                                                @endphp
                                                {{ $pendingOrder }}
                                            </span>
                                        </a>
                                    </li>
                                @elseif ($key == \App\Enums\ParcelStatus::DELIVERY_MAN_ASSIGN)
                                    <li class="nav-item" role="presentation">
                                        <a class="mt-2 nav-link btn btn-active {{ $request->parcel_status == $key ? 'active' : '' }} me-2"
                                            href="{{ route('merchant-panel.active.live.monitoring', ['parcel_status' => $key]) }}">{{ $status }}
                                            <span class="badge badge-primary">
                                                @php 
                                                    $deliveryManAssign = \App\Models\Backend\Parcel::wherein('status', [
                                                        App\Enums\ParcelStatus::DELIVERY_MAN_ASSIGN,
                                                        App\Enums\ParcelStatus::DELIVERY_RE_SCHEDULE,
                                                        App\Enums\ParcelStatus::CONFIRMED,
                                                        App\Enums\ParcelStatus::CONFIRMED_BOOKING,
                                                        App\Enums\ParcelStatus::UNCONFIRMED,
                                                        App\Enums\ParcelStatus::UNCONFIRMED_BOOKING,
                                                    ])->count();
                                                @endphp
                                                {{ $deliveryManAssign }}
                                            </span>
                                        </a>
                                    </li>
                                @elseif ($key == \App\Enums\ParcelStatus::PROCESSING)
                                    <li class="nav-item" role="presentation">
                                        <a class="mt-2 nav-link btn btn-active {{ $request->parcel_status == $key ? 'active' : '' }} me-2"
                                            href="{{ route('merchant-panel.active.live.monitoring', ['parcel_status' => $key]) }}">{{ $status }}
                                            <span class="badge badge-primary">
                                                @php 
                                                    $processing = \App\Models\Backend\Parcel::wherein('status', [
                                                        App\Enums\ParcelStatus::PROCESSING,
                                                        App\Enums\ParcelStatus::HEADING_TO_PICKUP_POINT,
                                                        App\Enums\ParcelStatus::PICKED_UP,
                                                        App\Enums\ParcelStatus::HEADING_TO_DELIVERY_POINT,
                                                        App\Enums\ParcelStatus::DROP_OFF_CITY,
                                                        App\Enums\ParcelStatus::DROP_OFf_HUB1,
                                                        App\Enums\ParcelStatus::HEADING_TO_DROP_OFF,
                                                        App\Enums\ParcelStatus::TRANSIT_OUT_CITY,
                                                        App\Enums\ParcelStatus::ON_THE_WAY_TO_CITY,
                                                        App\Enums\ParcelStatus::ARRIVED_AT_CITY,
                                                        App\Enums\ParcelStatus::DELIVERY_MAN_ASSIGN_SECOND_PROVINCE,
                                                    ])->count();
                                                @endphp
                                                {{ $processing }}
                                            </span>
                                        </a>
                                    </li>
                                @endif
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
                                        <th>{{ __('parcel.recipient_info') }}</th>
                                        <th>{{ __('parcel.location') }}</th>
                                        <th>{{ __('parcel.distance') }}</th>
                                        <th>{{ __('levels.details') }}</th>
                                        <th>{{ __('parcel.amount') }}</th>
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
                                                        class="btn btn-sm ml-2 bnone">...</button>
                                                    <div class="dropdown-menu">
                                                        <a href="{{ route('merchant-panel.parcel.details', $parcel->id) }}"
                                                            class="dropdown-item"><i class="fa fa-eye"
                                                                aria-hidden="true"></i> {{ __('levels.view') }}</a>
                                                        <a href="{{ route('merchant-panel.parcel.logs', $parcel->id) }}"
                                                            class="dropdown-item"><i class="fas fa-history"
                                                                aria-hidden="true"></i> {{ __('levels.parcel_logs') }}</a>
                                                        <a href="{{ route('merchant-parcel.clone', $parcel->id) }}"
                                                            class="dropdown-item"><i class="fas fa-clone"
                                                                aria-hidden="true"></i> {{ __('levels.clone') }}</a>
                                                        @if (\App\Enums\ParcelStatus::DELIVERED !== $parcel->status)
                                                            @if ($parcel->status == App\Enums\ParcelStatus::PENDING)
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
                                            <td>#{{ $parcel->tracking_id }}<br/> 
                                                <a href="{{ route('merchant-panel.parcel.logs', $parcel->id) }}" class="btn btn-outline-primary mt-2" target="_blank">{{ __('parcel.tracking') }} <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                                           </td>
                                            <td class="merchantpayment">
                                                <div class="w150">
                                                    <div class="d-flex">
                                                        <i class="fa fa-user"></i>&nbsp;<p>{{ $parcel->customer_name }}
                                                        </p>
                                                    </div>
                                                    <div class="d-flex">
                                                        <i class="fas fa-phone"></i>&nbsp;<p>{{ $parcel->customer_phone }}
                                                        </p>
                                                    </div>
                                                    <div class="d-flex">
                                                        <i class="fas fa-map-marker-alt"></i>&nbsp;<p>
                                                            {{ $parcel->customer_address }}</p>
                                                    </div>
                                                </div>
                                            </td>
 
                                            <td>
                                                <div class="border-box">
                                                    <div class="border-dotted"></div>
                                                    <div  ><i class="fa-regular fa-circle me-2 fs-7 text-primary"></i>{{ $parcel->pickup_location }}</div><br />
                                                    <div class="mt-2"><i class="fa fa-location-dot me-2 fs-6 text-primary"></i>{{ $parcel->drop_location }}</div>
                                                </div> 
                                            </td>
                                            <td>
                                           {{ $parcel->distance_km }} km
                                            </td>
                                            <td>
                                                {{ __('parcel.pickup_date') }}: <span class="text-dark">
                                                    {{ dateFormat($parcel->pickup_date) }}</span>
                                                <br>
                                                <b>{{ __('parcel.delivery_type') }} :</b>
                                                {{ @$parcel->DelivType->title }}<br />
                                                <b>{{ __('parcel.shipping_type') }} :</b>
                                                {{ @$parcel->ShippingType->title }} 
                                           
                                            </td>

                                            <td>
                                                <div class="w250">
                                                    {{ __('levels.cod') }}: <span
                                                        class="text-dark">{{ settings()->currency }}{{ $parcel->cash_collection }}</span>
                                                    <br>
                                                    {{ __('levels.total_delivery_amount') }}: <span
                                                        class="text-dark">{{ settings()->currency }}{{ $parcel->total_delivery_amount }}</span>
                                                    <br>
                                                    {{ __('parcel.discount') }} (-): <span
                                                        class="text-dark">{{ settings()->currency }}{{ $parcel->discount_amount }}</span>
                                                    <br>
                                                    {{ __('levels.vat_amount') }}: <span
                                                        class="text-dark">{{ settings()->currency }}{{ $parcel->vat_amount }}</span>
                                                    <br>
                                                    {{ __('levels.current_payable') }}:
                                                    <b>{{ settings()->currency }}{{ $parcel->current_payable }}</b>
                                                    <br>
                                                </div>
                                            </td>
                                            <td>{!! @$parcel->isSmallBatch !!}</td>
                                            <td>
                                                <p>{!! $parcel->parcel_status !!}</p>
                                                <span>{{ __('parcel.updated_on') }}:
                                                    {{ \Carbon\Carbon::parse($parcel->updated_at)->format('Y-m-d h:i:s A') }}</span>
                                            </td>
                                            
                                            <td>
                                                @php
                                                    if ($parcel->parcel_invoice !== null && $parcel->parcel_invoice->status == App\Enums\InvoiceStatus::PAID):
                                                        $status = $parcel->parcel_invoice->status;
                                                    elseif ($parcel->parcel_invoice !== null && $parcel->parcel_invoice->status == App\Enums\InvoiceStatus::UNPAID):
                                                        $status = App\Enums\InvoiceStatus::UNPAID;
                                                    elseif ($parcel->parcel_invoice !== null):
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
                                                    {{ @$parcel->parcel_invoice->invoice_id }}<br />
                                                    @if ($parcel->parcel_invoice !== null && $parcel->parcel_invoice->status == App\Enums\InvoiceStatus::PAID)
                                                        Paid At: {{ @dateFormat($parcel->parcel_invoice->updated_at) }}
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


            $(document).on('change', '#delivery_type_id', function() {

                var url = $(this).data('url');
                $.ajax({
                    type: 'get',
                    url: url,
                    data: {
                        'delivery_type_id': $(this).val()
                    },
                    dataType: "html",
                    success: function(data) {

                        $('#shipping_type').html(data);
                    }
                });
            });
            //multiple parcel label print

            $("#from_province_id").on("change", function () {
                var url = $(this).data("url");
                var id = $(this).val();
                $("#from_city_id").html("");
                $.ajax({
                    type: "POST",
                    url: url,
                    data: { id },
                    success: function (data) {
                        var cityOptions = "";
                        if (data && data.city_option) {
                            cityOptions = data.city_option;
                        } else if (Array.isArray(data)) {
                            cityOptions = '<option value="">---Select City---</option>';
                            data.forEach(function (city) {
                                cityOptions += '<option value="' + city.id + '">' + city.name + "</option>";
                            });
                        }
                        $("#from_city_id").html(cityOptions);
                    },
                });
            });

            $("#to_province_id").on("change", function () {
                var url = $(this).data("url");
                var id = $(this).val();
                $("#to_city_id").val("");
                $.ajax({
                    type: "POST",
                    url: url,
                    data: { id },
                    success: function (data) {
                        var cityOptions = "";
                        if (data && data.city_option) {
                            cityOptions = data.city_option;
                        } else if (Array.isArray(data)) {
                            cityOptions = '<option value="">---Select City---</option>';
                            data.forEach(function (city) {
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
                    data: {'delivery_type_id': delivery_type_id},
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
                } else if(delivery_type_id == 3) {
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
