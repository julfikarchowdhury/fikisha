@extends('backend.partials.master')
@section('title')
    {{ __('parcel.shipping_type') }} {{ __('levels.list') }}
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
                                <li class="breadcrumb-item"><a href="#"
                                        class="breadcrumb-link">{{ __('menus.settings') }}</a></li>
                                <li class="breadcrumb-item"><a href=""
                                        class="breadcrumb-link">{{ __('parcel.shipping_type') }}</a></li>
                                <li class="breadcrumb-item"><a href=""
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
                    <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                        <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('parcel.inside_city') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table   " style="width:100%">
                                <thead>
                                    <tr>
                                        <th>{{ __('levels.id') }}</th>
                                        <th>{{ __('levels.delivery_type') }}</th>
                                        <th>{{ __('levels.title') }}</th>
                                        <th>{{ __('levels.basic_price') }}</th>
                                        <th>{{ __('levels.weight_pricing') }}</th>
                                        <th>{{ __('levels.volume_pricing') }}</th> 
                                        <th>{{ __('levels.slots') }}</th> 
                                        @if (hasPermission('shipping_type_update') || hasPermission('shipping_type_delete'))
                                            <th>{{ __('levels.actions') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    @php $i=1; @endphp
                                    @foreach ($inside_shipping_types as $shipping_type)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ @$shipping_type->deliveryType->title }}</td>
                                            <td>{{ $shipping_type->title }}</td>
                                            <td>
                                                {{ settings()->currency }} {{ $shipping_type->basic_price }}
                                            </td>
                                            <td>
                                                {{ __('levels.weight') }} :
                                                {{ $shipping_type->start_weight . ' - ' . $shipping_type->end_weight }} =
                                                {{ $shipping_type->end_weight - $shipping_type->start_weight }} first frame
                                                free <br />
                                                {{ __('levels.addi_price') }} : {{ settings()->currency }}
                                                {{ $shipping_type->addi_weight_price }}
                                            </td>
                                            <td>
                                                {{ __('levels.cubic_meter') }} :
                                                {{ $shipping_type->start_volume . ' - ' . $shipping_type->end_volume }} =
                                                {{ $shipping_type->end_volume - $shipping_type->start_volume }} first frame
                                                free <br />
                                                {{ __('levels.addi_price') }} : {{ settings()->currency }}
                                                {{ $shipping_type->addi_volume_price }}
                                            </td>
                                            <td>
                                                {{ @$shipping_type->slots }}
                                            </td>
 
                                            @if (hasPermission('shipping_type_update') || hasPermission('shipping_type_delete'))
                                                <td>
                                                    <div class="row">
                                                        <button tabindex="-1" data-toggle="dropdown" type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split"> 
                                                            <i class="fa fa-cogs"></i>
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            @if (hasPermission('shipping_type_update'))
                                                                <a href="{{ route('shipping-type.edit', $shipping_type->id) }}"
                                                                    class="dropdown-item"><i class="fas fa-edit"
                                                                        aria-hidden="true"></i> {{ __('levels.edit') }}</a>
                                                            @endif
                                                            
                                                        </div>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="pb-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <span>{{ $inside_shipping_types->links() }}</span>
                        <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
                            {!! __('Showing') !!}
                            <span class="font-medium">{{ $inside_shipping_types->firstItem() }}</span>
                            {!! __('to') !!}
                            <span class="font-medium">{{ $inside_shipping_types->lastItem() }}</span>
                            {!! __('of') !!}
                            <span class="font-medium">{{ $inside_shipping_types->total() }}</span>
                            {!! __('results') !!}
                        </p>
                    </div>
                </div>


                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                        <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('parcel.outside_city') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table   " style="width:100%">
                                <thead>
                                    <tr>
                                        <th>{{ __('levels.id') }}</th>
                                        <th>{{ __('levels.delivery_type') }}</th>
                                        <th>{{ __('levels.title') }}</th>
                                        <th>{{ __('levels.basic_price') }} <small>(KM Based)</small></th>
                                        <th>{{ __('levels.weight_pricing') }}</th>
                                        <th>{{ __('levels.volume_pricing') }}</th> 
                                        <th>{{ __('levels.slots') }}</th> 
                                        @if (hasPermission('shipping_type_update') || hasPermission('shipping_type_delete'))
                                            <th>{{ __('levels.actions') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i=1; @endphp
                                    @foreach ($outside_shipping_types as $outside_shipping_type)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ @$outside_shipping_type->deliveryType->title }}</td>
                                            <td>{{ $outside_shipping_type->title }}</td>
                                            <td> 
                                                @foreach ($outside_shipping_type->ShippingChargeOptions as $option) 
                                                   From {{ $option->from_km }} {{ __('levels.km') }} To {{ $option->to_km }} {{ __('levels.km') }} = {{ settings()->currency }} {{ $option->basic_price }}<br/>
                                                @endforeach
                                            </td>
                                            <td>
                                                {{ __('levels.weight') }} :
                                                {{ $outside_shipping_type->start_weight . ' - ' . $outside_shipping_type->end_weight }} =
                                                {{ $outside_shipping_type->end_weight - $outside_shipping_type->start_weight }} first frame
                                                free <br />
                                                {{ __('levels.addi_price') }} : {{ settings()->currency }}
                                                {{ $outside_shipping_type->addi_weight_price }}
                                            </td>
                                            <td>
                                                {{ __('levels.cubic_meter') }} :
                                                {{ $outside_shipping_type->start_volume . ' - ' . $outside_shipping_type->end_volume }} =
                                                {{ $outside_shipping_type->end_volume - $outside_shipping_type->start_volume }} first frame
                                                free <br />
                                                {{ __('levels.addi_price') }} : {{ settings()->currency }}
                                                {{ $outside_shipping_type->addi_volume_price }}
                                            </td>
                                            <td>
                                                {{ $outside_shipping_type->slots }}
                                            </td>

                                            @if (hasPermission('shipping_type_update') || hasPermission('shipping_type_delete'))
                                                <td>
                                                    <div class="row">
                                                        <button tabindex="-1" data-toggle="dropdown" type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split"> 
                                                            <i class="fa fa-cogs"></i>
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            @if (hasPermission('shipping_type_update'))
                                                                <a href="{{ route('shipping-type.edit', $outside_shipping_type->id) }}"
                                                                    class="dropdown-item"><i class="fas fa-edit"
                                                                        aria-hidden="true"></i> {{ __('levels.edit') }}</a>
                                                            @endif
                                                            
                                                        </div>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="pb-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <span>{{ $outside_shipping_types->links() }}</span>
                        <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
                            {!! __('Showing') !!}
                            <span class="font-medium">{{ $outside_shipping_types->firstItem() }}</span>
                            {!! __('to') !!}
                            <span class="font-medium">{{ $outside_shipping_types->lastItem() }}</span>
                            {!! __('of') !!}
                            <span class="font-medium">{{ $outside_shipping_types->total() }}</span>
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
