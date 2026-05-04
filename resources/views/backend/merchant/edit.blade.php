@extends('backend.partials.master')
@section('title')
    {{ __('merchant.title') }}  {{ __('levels.edit') }}
@endsection
@section('maincontent')
<style>
    .individual {
        display: none;
    }
    .business {
        display: none;
    }
</style>
<div class="container-fluid  dashboard-content">
    <!-- pageheader -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="page-header">
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard.index')}}" class="breadcrumb-link">{{ __('levels.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">{{ __('merchantmanage.title') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{route('customer.index') }}" class="breadcrumb-link">{{ __('merchant.title') }}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{ __('levels.edit') }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- end pageheader -->
    <div class="row">
        <!-- basic form -->
        <div class="col-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('merchant.edit_merchant') }}</h4>
                </div>
                <div class="card-body">
                    <!-- <h2 class="pageheader-title">{{ __('merchant.edit_merchant') }}</h2> -->
                    <form action="{{route('customer.update',$merchant)}}"  method="POST" enctype="multipart/form-data" id="basicform">
                        @method('PUT')
                        @csrf
                        <input type="hidden" name="id" id="id" value="{{$merchant->id}}">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="province_id">{{ __('levels.province') }}<span class="text-danger">*</span></label>
                                    <select id="province_id" name="province_id" class="form-control select2">
                                        <option value="">{{ __('levels.select') }} {{ __('levels.province') }}</option>
                                        @foreach (\App\Models\Backend\Province::all() as $province)
                                            <option value="{{ $province->id }}" @selected(old('province_id',$merchant->user->province_id) == $province->id)>
                                                {{ $province->name }}({{ $province->province_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('province_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="city_id">{{ __('city.title') }}<span class="text-danger">*</span></label>
                                    <select id="city_id" name="city_id" class="form-control select2">
                                        <option value="">{{ __('levels.select') }} {{ __('city.title') }}</option>
                                        @foreach (\App\Models\Backend\City::all() as $city)
                                            @if (old('province_id',$merchant->user->province_id) == $city->province_id)
                                                <option value="{{ $city->id }}" @selected(old('city_id',$merchant->user->city_id) == $city->id)>
                                                    {{ $city->name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('city_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="first_name">{{ __('levels.first_name') }}</label> <span class="text-danger">*</span>
                                    <input id="first_name" type="text" name="first_name" placeholder="{{ __('placeholder.Enter_first_name') }}" autocomplete="off" class="form-control" value="{{old('first_name',$merchant->user->first_name)}}" require>
                                    @error('first_name')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="last_name">{{ __('levels.last_name') }}</label> <span class="text-danger">*</span>
                                    <input id="last_name" type="text" name="last_name" placeholder="{{ __('placeholder.Enter_last_name') }}" autocomplete="off" class="form-control" value="{{old('last_name',$merchant->user->last_name)}}" require>
                                    @error('last_name')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <input id="merchant_unique_id" type="hidden" name="merchant_unique_id" class="form-control">
                                <div class="form-group">
                                    <label for="business_name">{{ __('levels.business_name') }}</label> <span class="text-danger">*</span>
                                    <input id="business_name" type="text" name="business_name" placeholder="{{ __('placeholder.enter_business_name') }}" autocomplete="off" class="form-control @error('business_name') is-invalid @enderror" value="{{ old('business_name',$merchant->business_name) }}" require>
                                    @error('business_name')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="email">{{ __('levels.email') }}</label>
                                    <input id="email" type="text" name="email" placeholder="{{ __('placeholder.enter_email') }}" autocomplete="off" class="form-control @error('email') is-invalid @enderror" value="{{ old('email',$merchant->user->email) }}">
                                    @error('email')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="mobile">{{ __('levels.mobile') }}</label> <span class="text-danger">*</span><br>
                                    <div class="input-group">
                                        <div class="input-group-prepend" style="height: 39px !important;border-top-left-radius: 6px !important;
                                        border-bottom-left-radius: 6px !important;">
                                            <span class="input-group-text" id="mobile" style="border-top-left-radius: 6px !important;border-bottom-left-radius: 6px !important;"> {{ settings()->country_code }}</span>
                                        </div>
                                        <input type="text" class="form-control form-control-one @error('mobile') is-invalid @enderror" style="
                                        border-radius: 0 !important;
                                        border-top-right-radius: 6px !important;
                                        border-bottom-right-radius: 6px !important;
                                        padding: 0.25rem 0.25rem !important;
                                        line-height: 1.5 !important;
                                        height: 39px !important;
                                        " name="mobile" placeholder="{{ __('levels.enter_phone_number') }}" value="{{ old('mobile',$merchant->user->mobile) }}" id="mobile" autocomplete="off">
                                        <input type="hidden" id="country_code" name="country_code" value="{{ old('country_code') }}" />
                                    </div>
                                    @error('mobile')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="alternative_phone_number">{{ __('levels.alternative_phone_number') }}</label><br>
                                    <div class="input-group">
                                        <div class="input-group-prepend" style="height: 39px !important;">
                                            <span class="input-group-text" id="alternative_phone_number" style="border-top-left-radius: 6px !important;border-bottom-left-radius: 6px !important;"> {{ settings()->country_code }}</span>
                                        </div>
                                        <input type="text" class="form-control form-control-one @error('alternative_phone_number') is-invalid @enderror" style="
                                        border-radius: 0 !important;
                                        border-top-right-radius: 6px !important;
                                        border-bottom-right-radius: 6px !important;
                                        padding: 0.25rem 0.25rem !important;
                                        line-height: 1.5 !important;
                                        height: 39px !important;
                                        " name="alternative_phone_number" placeholder="{{ __('levels.enter_phone_number') }}" value="{{ old('alternative_phone_number',$merchant->user->alternative_phone_number) }}" id="alternative_phone_number" autocomplete="off">
                                    </div>
                                    @error('alternative_phone_number')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="email">{{ __('levels.password') }}</label>
                                    <input id="password" type="password" name="password" placeholder="{{ __('placeholder.Enter_password') }}" autocomplete="off" class="form-control @error('password') is-invalid @enderror"  value="{{old('password')}}">
                                    @error('password')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="nid">{{ __('levels.nid') }} (Front Side)</label>
                                    <div class="row">
                                        <div class="col-9">
                                            <input id="nid" type="file" name="nid" autocomplete="off" class="form-control @error('nid') is-invalid @enderror" value="{{ old('nid') }}" require>
                                            @error('nid')
                                            <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        @if($merchant->nid)
                                            <div class="col-3">
                                                <img src="{{ $merchant->nid }}" alt="user" class="rounded"  width="40" height="40">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="nid_back">{{ __('levels.nid') }} (Back Side)</label>
                                    <div class="row">
                                        <div class="col-9">
                                            <input id="nid" type="file" name="nid_back" autocomplete="off" class="form-control" value="{{ old('nid_back') }}" require>
                                            @error('nid_back')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div> 
                                        @if($merchant->nid_back)
                                        <div class="col-3">
                                            <img src="{{ $merchant->nid_back }}" alt="user" class="rounded"  width="40" height="40">
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="image">{{ __('levels.profile_picture') }}</label>
                                    <div class="row">
                                        <div class="col-9">
                                            <input id="image_id" type="file" name="image_id" autocomplete="off" class="form-control @error('image_id') is-invalid @enderror">
                                            @error('image_id')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        @if($merchant->user->image)
                                            <div class="col-3">
                                                <img src="{{ $merchant->user->image }}" alt="user" class="rounded"  width="40" height="40">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 business">
                                <div class="form-group">
                                    <label for="trade_license">{{ __('levels.trade_license') }}</label>
                                    <div class="row">
                                        <div class="col-9">
                                            <input id="trade_license" type="file" name="trade_license" autocomplete="off" class="form-control @error('trade_license') is-invalid @enderror" >
                                            @error('trade_license')
                                            <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        @if($merchant->trade)
                                            <div class="col-3">
                                                <img src="{{ $merchant->trade }}" alt="user" class="rounded"  width="40" height="40">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group ">
                                    <label for="opening_balance">{{ __('levels.opening_balance') }}</label>
                                    <input id="opening_balance" type="number" name="opening_balance" placeholder="{{ __('placeholder.Enter_opening_balance') }}" autocomplete="off" class="form-control @error('opening_balance') is-invalid @enderror" value="{{ old('opening_balance',$merchant->opening_balance) }}">
                                    @error('opening_balance')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="opening_balance">{{ __('levels.vat') }}</label>
                                    <input id="vat" type="number" name="vat" placeholder="{{ __('placeholder.Enter_vat') }}" autocomplete="off" class="form-control @error('vat') is-invalid @enderror" value="{{ old('vat',$merchant->vat) }}">
                                    @error('vat')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="opening_balance">{{ __('parcel.minimum_reaches_amount') }}</label>
                                    <input id="minimum_reaches_amount" type="text" name="minimum_reaches_amount" placeholder="{{ __('parcel.Enter_minimum_reaches_amount') }}" autocomplete="off" class="form-control" value="{{old('minimum_reaches_amount',$merchant->minimum_reaches_amount)}}">
                                    @error('minimum_reaches_amount')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="opening_balance">{{ __('parcel.discount') }} (%)</label>
                                    <input id="discount" type="text" name="discount" placeholder="{{ __('parcel.Enter_discount') }}" autocomplete="off" class="form-control" value="{{old('discount',$merchant->discount)}}">
                                    @error('discount')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="status">{{ __('levels.status') }}</label> <span class="text-danger">*</span>
                                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                                        @foreach(trans('status') as $key => $status)
                                            <option value="{{ $key }}" {{ (old('status',$merchant->user->status) == $key) ? 'selected' : '' }}>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group  col-md-6 ">
                                <label for="pickup_location">Location</label> <span class="text-danger">*</span>
                                <input type="hidden" id="pickup_lat" name="location_lat" value="{{ old('location_lat', $merchant->user->latitude) }}">
                                <input type="hidden" id="pickup_long" name="location_long" value="{{ old('location_long', $merchant->user->longitude) }}">
                                <div class="main-search-input-item location location-search">
                                    <div id="autocomplete-container" class="form-group random-search">
                                        <input id="autocomplete-input" type="text" name="location" class="recipe-search2 mb-2 form-control" value="{{ old('location', $merchant->user->location) }}" placeholder="Location Here!" required>
                                        <a href="javascript:void(0)" class="submit-btn btn current-location" id="locationIcon" onclick="getLocation()">
                                            <i class="fa fa-crosshairs"></i>
                                        </a>
                                    </div>
                                </div>
                                @if (empty(env('GOOGLE_MAPS_API_KEY')))
                                    <small class="text-muted">Set GOOGLE_MAPS_API_KEY in .env to enable map search.</small>
                                @endif
                                <div id="googleMap" class="custom-map d-none"></div>
                                @error('location')
                                    <span class="text-danger mt-2">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label for="contract_document">{{ __('levels.contract_document') }}
                                        @if ($merchant->my_contract_document)
                                            <a href="{{ $merchant->my_contract_document }}" target="_blank" class="text-danger">(View File)</a>
                                        @endif
                                    </label>
                                    <input type="file" id="contract_document" name="contract_document" class="form-control" accept="*">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group  ">
                                    <label for="return_charges">{{ __('levels.return_charges') }} (%)</label>
                                    <input type="number" id="return_charges" placeholder="{{ __('levels.return_charges') }}" name="return_charges" class="form-control" value="{{ old('return_charges',$merchant->return_charges) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="business">
                                    <div class="form-group">
                                        <label for="business_address">{{ __('levels.business_address') }}</label>  <span class="text-danger">*</span>
                                        <textarea id="business_address" name="business_address" placeholder="{{ __('placeholder.Enter_business_address') }}" class="form-control">{{old('business_address',$merchant->address)}}</textarea>
                                        @error('business_address')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <div class="individual">
                                    <div class="form-group  ">
                                        <label for="address">{{ __('levels.address') }}</label>  <span class="text-danger">*</span>
                                        <textarea id="address" name="address" placeholder="{{ __('placeholder.Enter_address') }}" class="form-control">{{old('address',$merchant->address)}}</textarea>
                                        @error('address')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-7 col-12">
                                <h2 for="input-select">{{ __('merchant.payment_date') }}</h2>
                            </div>
                            <div class="col-lg-7 col-12">
                                <table id="paymentDate" class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th scope="col">{{ __('menus.date') }}<span class="text-danger">*</span></th>
                                            <th scope="col" class="text-center" style="width: 10%;">
                                                <button type="button" class="btn btn-success btn-sm" onclick="addRow()">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($merchant_payment_dates) > 0)
                                            @foreach($merchant_payment_dates as $key => $merchant_payment_date)
                                                <tr>
                                                    <td><input type="date" name="payment_date[]" value="{{ $merchant_payment_date->date }}" class="form-control" required /></td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-danger btn-sm remove">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td><input type="date" name="payment_date[]" class="form-control" required /></td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-sm remove">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row d-none">
                            <div class="col-12">
                                <div class="form-group">
                                    <h2 for="input-select">{{ __('levels.cod_charge') }}</h2>
                                    <div class="row">
                                        @foreach($merchant->cod_charges as $key=>$charge)
                                        <input type="hidden" value="{{$key}}" name="area[]">
                                        <div class="col-12 col-md-4">
                                            <label class="select-input">{{str_replace('_', ' ',  ucwords($key))}}</label>
                                            <input type="number" name="charge[{{$key}}]" autocomplete="off" class="form-control" value="{{old('charge.'.$key,$charge)}}" placeholder="charge">
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 ">
                                <button type="submit" class="btn btn-space btn-primary">{{ __('levels.save_change') }}</button>
                                <a href="{{ route('customer.index') }}" class="btn btn-space btn-secondary">{{ __('levels.cancel') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- end basic form -->
    </div>
</div>
<!-- end wrapper  -->
@endsection()
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .custom-map {
        width: 100%;
        height: 17rem;
        border: 1px solid #696cff;
        border-radius: 5px;
    }

    .main-search-input-item {
        flex: 1;
        margin-top: 3px;
        position: relative;
    }

    #autocomplete-container,
    #autocomplete-input {
        position: relative;
        z-index: 101;
    }

    .main-search-input input,
    .main-search-input input:focus {
        font-size: 16px;
        border: none;
        background: #fff;
        margin: 0;
        padding: 0;
        height: 44px;
        line-height: 44px;
        box-shadow: none;
    }

    .input-with-icon i,
    .main-search-input-item.location a {
        padding: 5px 10px;
        z-index: 101;
    }

    .main-search-input-item.location a {
        position: absolute;
        right: -50px;
        top: 40%;
        transform: translateY(-50%);
        color: #999;
        padding: 10px;
    }

    .current-location {
        margin-right: 50px;
        margin-top: 5px;
        color: var(--bs-primary) !important;
    }

    #bookingDate {
        display: none;
    }
    .form-control-one {
        border-top-right-radius: 30px !important;
        border-bottom-right-radius: 30px !important;
        padding: 0.25rem 1rem !important;
        line-height: 1.5 !important;
    }
</style>
@endpush
@push('scripts')
<script src="{{static_asset('backend')}}/vendor/jquery/jquery-3.3.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function(){
            $('.select2').select2();
        });
    </script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places"></script>
<script type="text/javascript">
    google.maps.event.addDomListener(window, "load", initialize);
    var geocoder;
    function initialize() {
        geocoder = new google.maps.Geocoder();
        var input = document.getElementById("autocomplete-input");
        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.addListener("place_changed", function() {
            var place = autocomplete.getPlace();
            $("#pickup_lat").val(place.geometry["location"].lat());
            $("#pickup_long").val(place.geometry["location"].lng());
            const LatlngData = {
                lat: parseFloat(place.geometry["location"].lat()),
                lng: parseFloat(place.geometry["location"].lng()),
            };
            new google.maps.Map(document.getElementById("googleMap"), {
                zoom: 15,
                center: LatlngData,
            });
        });
    }

    function getLocation() {
        geocoder = new google.maps.Geocoder();
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                getLatLongPosition(
                    position.coords.latitude,
                    position.coords.longitude
                );
                var Latitude = position.coords.latitude;
                var Longitude = position.coords.longitude;
                var latlng = new google.maps.LatLng(Latitude, Longitude);
                geocoder.geocode({
                        latLng: latlng,
                    },
                    function(results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            if (results[1]) {
                                $("#autocomplete-input").val(
                                    results[0].formatted_address
                                );
                            }
                        }
                    }
                );
            });
        } else {
            var msg = "Geolocation is not supported by this browser.";
        }
    }

    function getLatLongPosition(latitude, longitude) {
        const myLatlng = {
            lat: parseFloat(latitude),
            lng: parseFloat(longitude),
        };
        const map = new google.maps.Map(document.getElementById("googleMap"), {
            zoom: 15,
            center: myLatlng,
        });
    }

    var latitudec = $("#pickup_lat").val();
    var longitudec = $("#pickup_long").val();
    if (latitudec && longitudec) {
        getPositionData();
    }

    function getPositionData() {
        var latitude1 = $("#pickup_lat").val();
        var longitude1 = $("#pickup_long").val();
        var latitude2 = $("#drop_latitude").val();
        var longitude2 = $("#drop_longitude").val();
        const myLatlngOne = {
            lat: parseFloat(latitude1),
            lng: parseFloat(longitude1),
        };
        const mapOne = new google.maps.Map(document.getElementById("googleMap"), {
            zoom: 15,
            center: myLatlngOne,
        }); 
    }
    function addRow(){
        var tr = '<tr>'+
                    '<td><input type="date" name="payment_date[]" class="form-control" required /></td>'+
                    '<td class="text-center"><button type="button" class="btn btn-danger btn-sm remove"><i class="fa fa-trash"></i></button></td>'+
                '</tr>';
        $('#paymentDate tbody').append(tr);
    }

    $(document).on("click", ".remove", function () {
        var length = $("#paymentDate tbody tr").length;
        if (length == 1) {
            alert('You can not remove last one');
        } else {
            $(this).parent().parent().remove();
        }
    });
    
    $("#province_id").on('change', function() {
        var id = $(this).val();
        var op = "";
        $.ajax({
            type: "GET",
            url: "{{ url('admin/province/wise/city') }}/"+id,
            dataType:'json',
            success: function(data){
                op +='<option  value="">--Select City--</option>';
                for (var i=0; i<data.length; i++) {
                    op +='<option  value="'+data[i].id+'">'+data[i].name+'</option>';
                }
                $('#city_id').html(op);
            }
        });
    });
</script>
@endpush
