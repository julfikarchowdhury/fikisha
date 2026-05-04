@extends('backend.partials.master')
@section('title')
{{ __('parcel.received_parcel') }} {{ __('levels.list') }}
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
                            <li class="breadcrumb-item"><a href="{{route('dashboard.index')}}" class="breadcrumb-link">{{ __('levels.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('merchant-panel.received.parcel.index') }}" class="breadcrumb-link">{{ __('parcel.received_parcel') }}</a></li>
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link active">{{ __('levels.list') }}</a></li>
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
                <div class="merchantParcelPage card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <div class="d-flex   parcelsearchFlex parcel-import-export-btn">
                        <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('parcel.received_parcel') }}</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table" class="table" style="width:100%">
                            <thead>
                                <tr>
                                    <th class="parcel-index permission-check-box">
                                        <input type="checkbox" id="tick-all" class="form-check-input" />
                                    </th>
                                    <th>{{ __('###') }}</th>
                                    <th>{{ __('parcel.tracking_id') }}</th>
                                    <th>{{ __('parcel.recipient_info') }}</th>
                                    <th>{{ __('parcel.amount')}}</th>
                                    <th>{{ __('parcel.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1; @endphp
                                @foreach($parcels as $parcel)
                                <tr>
                                    <td class="parcel-index permission-check-box">
                                        <input type="checkbox" name="parcels[][{{ $parcel->id }}]" value="{{ $parcel->id }}" class="common-key form-check-input" />
                                    </td>
                                    <td>
                                        <div class="row">
                                            <button tabindex="-1" data-toggle="dropdown" type="button" class="btn btn-sm ml-2 bnone">...</button>
                                            <div class="dropdown-menu">
                                                <a href="{{ route('merchant-panel.parcel.details',$parcel->id) }}" class="dropdown-item"><i class="fa fa-eye" aria-hidden="true"></i> {{__('levels.view')}}</a>
                                                <a href="{{ route('merchant-panel.parcel.logs',$parcel->id) }}" class="dropdown-item"><i class="fas fa-history" aria-hidden="true"></i> {{__('levels.parcel_logs')}}</a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $parcel->tracking_id }}</td>
                                    <td class="merchantpayment">
                                        <div class="w150">
                                            <div class="d-flex">
                                                <i class="fa fa-user"></i>&nbsp;<p>{{$parcel->customer_name}}</p>
                                            </div>
                                            <div class="d-flex">
                                                <i class="fas fa-phone"></i>&nbsp;<p>{{$parcel->customer_phone}}</p>
                                            </div>
                                            <div class="d-flex">
                                                <i class="fas fa-map-marker-alt"></i>&nbsp;<p>{{$parcel->customer_address}}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="w250">
                                            {{__('levels.cod')}}: <span class="text-dark">{{settings()->currency}}{{$parcel->cash_collection}}</span>
                                            <br>
                                            {{__('levels.total_delivery_amount')}}: <span class="text-dark">{{settings()->currency}}{{$parcel->total_delivery_amount}}</span>
                                            <br>
                                            {{__('levels.vat_amount')}}: <span class="text-dark">{{settings()->currency}}{{$parcel->vat_amount}}</span>
                                            <br>
                                            {{__('levels.current_payable')}}: <b>{{settings()->currency}}{{$parcel->current_payable}}</b>
                                            <br>
                                        </div>
                                    </td>
                                    <td>
                                        <p>{!! $parcel->parcel_status !!}</p>
                                        <span>{{__('parcel.updated_on')}}: {{\Carbon\Carbon::parse($parcel->updated_at)->format('Y-m-d h:i:s A')}}</span>
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