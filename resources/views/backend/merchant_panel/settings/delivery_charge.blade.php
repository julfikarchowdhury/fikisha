@extends('backend.partials.master')
@section('title')
    {{ __('delivery_charge.title') }} {{ __('levels.lsit') }}
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
                                <li class="breadcrumb-item"><a href=""
                                        class="breadcrumb-link">{{ __('menus.settings') }}</a></li>
                                <li class="breadcrumb-item"><a href=""
                                        class="breadcrumb-link">{{ __('delivery_charge.title') }}</a></li>
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
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                        <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">Delivery Charges</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-3 h-100 card mb-0">
                                    <div class="small text-muted" style="font-size: 14px;">Pricing Mode</div>
                                    <div class="font-weight-bold mt-1" style="font-size: 16px;">Inside city / Outside city</div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-3 h-100 card mb-0">
                                    <div class="small text-muted" style="font-size: 14px;">Inside city max distance (km)</div>
                                    <div class="font-weight-bold mt-1" style="font-size: 16px;">{{ $settings->inside_city_distance ?? 0 }}</div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-3 h-100 card mb-0">
                                    <div class="small text-muted" style="font-size: 14px;">Receiver pays markup (%)</div>
                                    <div class="font-weight-bold mt-1" style="font-size: 16px;">{{ $settings->marketplace_receiver_markup_percent ?? 0 }}%</div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3">{{ __('parcel.inside_city') }}</h5>
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Base fare</span>
                                        <span>{{ settings()->currency }} {{ $settings->inside_city_base_fare ?? 0 }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Per km rate</span>
                                        <span>{{ settings()->currency }} {{ $settings->inside_city_per_km_rate ?? 0 }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Per kg rate</span>
                                        <span>{{ settings()->currency }} {{ $settings->inside_city_per_kg_rate ?? 0 }}</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5 class="mb-3">{{ __('parcel.outside_city') }}</h5>
                                <ul class="list-group">
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Base fare</span>
                                        <span>{{ settings()->currency }} {{ $settings->outside_city_base_fare ?? 0 }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Per km rate</span>
                                        <span>{{ settings()->currency }} {{ $settings->outside_city_per_km_rate ?? 0 }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span>Per kg rate</span>
                                        <span>{{ settings()->currency }} {{ $settings->outside_city_per_kg_rate ?? 0 }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end wrapper  -->
    @endsection()
