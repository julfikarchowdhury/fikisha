@extends('backend.partials.master')
@section('title')
    Marketplace Pricing
@endsection
@section('maincontent')
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="page-header">
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                        class="breadcrumb-link">{{ __('levels.dashboard') }}</a></li>
                                <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">{{ __('menus.settings') }}</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('delivery-charge.index') }}"
                                        class="breadcrumb-link">Marketplace Pricing</a></li>
                                <li class="breadcrumb-item"><a href=""
                                        class="breadcrumb-link active">{{ __('levels.create') }}</a></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                        <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('delivery_charge.create_delivery_charge') }}</h4>
                    </div>
                    <div class="card-body">
                        <!-- <h2 class="pageheader-title">Marketplace Pricing</h2> -->
                        <form action="{{ route('delivery-charge.store') }}" method="POST" id="basicform">
                            @csrf
                            <div class="row">
                                <div class="form-group col-12 col-md-6">
                                    <label>Pricing mode</label> <span class="text-danger">*</span>
                                    <input type="text" class="form-control" value="Inside city / Outside city" disabled>
                                    <input type="hidden" name="marketplace_pricing_mode" value="city">
                                </div>
                                <div class="form-group col-12 col-md-6">
                                    <label for="inside_city_distance">Inside city max distance (km)</label> <span class="text-danger">*</span>
                                    <input id="inside_city_distance" type="number" step="0.01" min="0" name="inside_city_distance"
                                        class="form-control" value="{{ old('inside_city_distance', $settings->inside_city_distance ?? 0) }}">
                                    @error('inside_city_distance')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group col-12 col-md-6">
                                    <label for="marketplace_receiver_markup_percent">Receiver pays markup (%)</label> <span class="text-danger">*</span>
                                    <input id="marketplace_receiver_markup_percent" type="number" step="0.01" min="0" max="100"
                                        name="marketplace_receiver_markup_percent" class="form-control"
                                        value="{{ old('marketplace_receiver_markup_percent', $settings->marketplace_receiver_markup_percent ?? 0) }}">
                                    @error('marketplace_receiver_markup_percent')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <hr>
                            <!-- <h5>Distance Pricing</h5>
                            <div class="row">
                                <div class="form-group col-12 col-md-6">
                                    <label for="marketplace_base_fare">Marketplace base fare</label> <span class="text-danger">*</span>
                                    <input id="marketplace_base_fare" type="number" step="0.01" min="0" name="marketplace_base_fare"
                                        class="form-control" value="{{ old('marketplace_base_fare', $settings->marketplace_base_fare ?? 0) }}">
                                    @error('marketplace_base_fare')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group col-12 col-md-6">
                                    <label for="marketplace_per_km_rate">Marketplace per km rate</label> <span class="text-danger">*</span>
                                    <input id="marketplace_per_km_rate" type="number" step="0.01" min="0" name="marketplace_per_km_rate"
                                        class="form-control" value="{{ old('marketplace_per_km_rate', $settings->marketplace_per_km_rate ?? 0) }}">
                                    @error('marketplace_per_km_rate')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group col-12 col-md-6">
                                    <label for="marketplace_per_kg_rate">Marketplace per kg rate</label> <span class="text-danger">*</span>
                                    <input id="marketplace_per_kg_rate" type="number" step="0.01" min="0" name="marketplace_per_kg_rate"
                                        class="form-control" value="{{ old('marketplace_per_kg_rate', $settings->marketplace_per_kg_rate ?? 0) }}">
                                    @error('marketplace_per_kg_rate')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                                
                            </div> -->
                            
                            <hr>
                            <h5>Inside City Pricing</h5>
                            <div class="row">
                                <div class="form-group col-12 col-md-6">
                                    <label for="inside_city_base_fare">Inside city base fare</label> <span class="text-danger">*</span>
                                    <input id="inside_city_base_fare" type="number" step="0.01" min="0" name="inside_city_base_fare"
                                        class="form-control" value="{{ old('inside_city_base_fare', $settings->inside_city_base_fare ?? 0) }}">
                                    @error('inside_city_base_fare')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group col-12 col-md-6">
                                    <label for="inside_city_per_km_rate">Inside city per km rate</label> <span class="text-danger">*</span>
                                    <input id="inside_city_per_km_rate" type="number" step="0.01" min="0" name="inside_city_per_km_rate"
                                        class="form-control" value="{{ old('inside_city_per_km_rate', $settings->inside_city_per_km_rate ?? 0) }}">
                                    @error('inside_city_per_km_rate')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group col-12 col-md-6">
                                    <label for="inside_city_per_kg_rate">Inside city per kg rate</label> <span class="text-danger">*</span>
                                    <input id="inside_city_per_kg_rate" type="number" step="0.01" min="0" name="inside_city_per_kg_rate"
                                        class="form-control" value="{{ old('inside_city_per_kg_rate', $settings->inside_city_per_kg_rate ?? 0) }}">
                                    @error('inside_city_per_kg_rate')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <hr>
                            <h5>Outside City Pricing</h5>
                            <div class="row">
                                <div class="form-group col-12 col-md-6">
                                    <label for="outside_city_base_fare">Outside city base fare</label> <span class="text-danger">*</span>
                                    <input id="outside_city_base_fare" type="number" step="0.01" min="0" name="outside_city_base_fare"
                                        class="form-control" value="{{ old('outside_city_base_fare', $settings->outside_city_base_fare ?? 0) }}">
                                    @error('outside_city_base_fare')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group col-12 col-md-6">
                                    <label for="outside_city_per_km_rate">Outside city per km rate</label> <span class="text-danger">*</span>
                                    <input id="outside_city_per_km_rate" type="number" step="0.01" min="0" name="outside_city_per_km_rate"
                                        class="form-control" value="{{ old('outside_city_per_km_rate', $settings->outside_city_per_km_rate ?? 0) }}">
                                    @error('outside_city_per_km_rate')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group col-12 col-md-6">
                                    <label for="outside_city_per_kg_rate">Outside city per kg rate</label> <span class="text-danger">*</span>
                                    <input id="outside_city_per_kg_rate" type="number" step="0.01" min="0" name="outside_city_per_kg_rate"
                                        class="form-control" value="{{ old('outside_city_per_kg_rate', $settings->outside_city_per_kg_rate ?? 0) }}">
                                    @error('outside_city_per_kg_rate')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 ">
                                    <button type="submit" class="btn btn-space btn-primary">{{ __('levels.save') }}</button>
                                    <a href="{{ route('delivery-charge.index') }}" class="btn btn-space btn-secondary">{{ __('levels.cancel') }}</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection()

