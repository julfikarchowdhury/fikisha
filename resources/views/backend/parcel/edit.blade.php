@extends('backend.partials.master')
@section('title')
    {{ __('parcel.title') }} {{ __('levels.edit') }}
@endsection
@section('maincontent')
    <div class="container-fluid  dashboard-content">
        <!-- pageheader -->
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="page-header">
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard.index') }}" class="breadcrumb-link">
                                        {{ __('parcel.dashboard') }}
                                    </a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('parcel.index') }}" class="breadcrumb-link">
                                        {{ __('parcel.title') }}
                                    </a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="" class="breadcrumb-link active">
                                        {{ __('levels.edit') }}
                                    </a>
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-12 col-xl-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                        <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('parcel.edit_parcel') }}</h4>
                    </div>
                    <div class="card-body">
                        <!-- <h2 class="pageheader-title">{{ __('parcel.edit_parcel') }}</h2> -->
                        <form action="{{ route('parcel.update', $parcel) }}" method="POST" enctype="multipart/form-data" id="basicform">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="border text-center p-2 mb-2 align-items-center">
                                        <h3 class="m-0 p-0">{{ __('parcel.sending_from') }}</h3>
                                    </div>
                                    <div class="form-group">
                                        <label for="from_state_id">{{ __('parcel.province_state') }} <span class="text-danger">*</span></label>
                                        <select name="from_state_id" id="from_state_id" class="form-control select2" style="width: 100%" data-url="{{ route('get.province.city') }}">
                                            <option value="">{{ __('menus.select') }} {{ __('parcel.province_state') }}</option>
                                            @foreach ($provinces as $province)
                                                <option value="{{ $province->id }}" @selected(old('from_state_id', $parcel->from_state_id) == $province->id)>{{ $province->name }}({{ $province->province_code }})</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" id="getProvinceAccountTypeWiseMerchant" data-url="{{ route('get.province.account_type.wise.merchant') }}" />
                                        <input type="hidden" id="urlCustomer" data-url="{{ route('get.merchant.customer') }}" />
                                        @error('from_state_id')
                                            <span class="text-danger mt-2">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <input type="hidden" name="from_account_type" id="from_account_type" value="1">
                                    <div class="form-group">
                                        <nav>
                                            <div class="nav nav-pills location-tabs from_point_button" id="nav-tab" role="tablist">
                                                <button class="nav-link @if (old('from_point_type', $parcel->from_point_type) == 1 || (!old('from_point_type', $parcel->from_point_type) && !request('from_point_type', $parcel->from_point_type))) active @endif" data-type="1" id="nav-home-tab" data-bs-toggle="tab"
                                                    data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home"
                                                    aria-selected="true">
                                                    {{ __('parcel.pickup_point') }}
                                                </button>
                                                <input type="hidden" id="from_point" value="1" name="from_point_type">
                                            </div>
                                        </nav>
                                    </div>
                                    <div class="form-group fromOnlyShowDoor">
                                        <label for="from_city_id">{{ __('parcel.city') }} <span class="text-danger">*</span></label>
                                        <select name="from_city_id" id="from_city_id" class="form-control select2" data-url="{{ route('city.wise.portal_code') }}" style="width: 100%">
                                            <option value="">{{ __('menus.select') }} {{ __('parcel.city') }}</option>
                                            @foreach ($cities as $fromCity)
                                                @if (old('from_state_id', $parcel->from_state_id) == $fromCity->province_id)
                                                    <option value="{{ $fromCity->id }}" @selected(old('from_city_id', $parcel->from_city_id) == $fromCity->id)>{{ $fromCity->name }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        @error('from_city_id')
                                            <span class="text-danger mt-2">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group fromOnlyShowDoor">
                                        <label for="from_portal_code">{{ __('parcel.portal_code') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="from_portal_code" id="from_portal_code" placeholder="{{ __('parcel.portal_code') }}"class="form-control" value="{{ old('from_portal_code',@$parcel->from_portal_code) }}">
                                        @error('from_portal_code')
                                            <span class="text-danger mt-2">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="border text-center p-2 mb-2 align-items-center">
                                        <h3 class="m-0 p-0">Sender</h3>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="row align-items-center">
                                                <div class="col-md-9">
                                                    <label for="merchant_id">{{ __('merchant.title') }}<span class="text-danger">*</span></label>
                                                    <select style="width: 100%" id="merchant_id" name="merchant_id" class="form-control select2" data-url="{{ route('parcel.merchant.shops') }}">
                                                        <option value=""> {{ __('menus.select') }} {{ __('merchant.title') }}</option>
                                                        @foreach($merchants as $key => $merchant)
                                                            @if(old('from_state_id', $parcel->from_state_id) == $merchant->user->province_id)
                                                                <option value="{{ @$merchant->id }}" @selected(old('merchant_id', $parcel->merchant_id) == $merchant->id)> {{ @$merchant->business_name }}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                    @error('merchant_id')
                                                        <small class="text-danger mt-2">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3">
                                                    <a class="btn btn-primary btn-sm mt-4"
                                                        href="{{ route('customer.create', ['from_parcel' => 'from_parcel']) }}">
                                                        <i class="fa fa-plus"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- cod charge calculation --}}
                                        @if (isset($merchant_info) && $merchant_info->business_name)
                                            @php
                                                $inside_city = $merchant_info->cod_charges['inside_city'];
                                                $sub_city = $merchant_info->cod_charges['sub_city'];
                                                $outside_city = $merchant_info->cod_charges['outside_city'];
                                            @endphp
                                        @endif
                                        <input type="hidden" id="merchanturl" data-url="{{ route('get.merchant.cod') }}" />
                                        <input type="hidden" id="inside_city" value="{{ old('inside_city',$inside_city??0) }}" />
                                        <input type="hidden" id="sub_city"value="{{ old('sub_city',$sub_city??0) }}" />
                                        <input type="hidden" id="outside_city" value="{{ old('outside_city',$outside_city??0) }}" />
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group merchant_info">
                                            <label for="shopID">{{ __('parcel.shop') }}</label>
                                            <select style="width: 100%" id="shopID" class="form-control" name="shop_id"
                                                data-url="{{ route('parcel.merchant.shops') }}">
                                                @foreach ($shops as $shop)
                                                    <option value="{{ $shop->id }}" {{ old('shop_id', $parcel->merchant_shop_id) == $shop->id ? 'selected' : '' }}>
                                                        {{ $shop->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('shop_id')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12" id="company_name_field" style="display: none">
                                        <div class="form-group">
                                            <label for="company_name">{{ __('levels.company_name') }}</label>
                                            <input id="company_name" type="text" name="company_name"
                                                placeholder="{{ __('placeholder.enter_company_name') }}"
                                                class="form-control" value="{{ old('company_name', $parcel->sender_company_name) }}">
                                            @error('company_name')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="first_name">{{ __('levels.first_name') }}</label>
                                            <input id="first_name" type="text" name="first_name"
                                                placeholder="{{ __('placeholder.Enter_first_name') }}"
                                                class="form-control" value="{{ old('first_name', $parcel->sender_first_name) }}">
                                            @error('first_name')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="last_name">{{ __('levels.last_name') }}</label>
                                            <input id="last_name" type="text" name="last_name"
                                                placeholder="{{ __('placeholder.Enter_last_name') }}"
                                                class="form-control" value="{{ old('last_name', $parcel->sender_last_name) }}">
                                            @error('last_name')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="pickup_phone">{{ __('parcel.phone_number') }}</label>
                                            <input id="pickup_phone" type="text" name="pickup_phone"
                                                placeholder="{{ __('levels.pickup') }} {{ __('levels.phone') }}"
                                                autocomplete="off" class="form-control"
                                                value="{{ old('pickup_phone', $parcel->pickup_phone) }}">
                                            @error('pickup_phone')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="sender_email">{{ __('levels.email') }}</label>
                                            <input placeholder="{{ __('levels.enter_email') }}"
                                                type="text" id="sender_email" name="sender_email"
                                                class="form-control" value="{{ old('sender_email', $parcel->sender_email) }}" />
                                            @error('sender_email')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="pickup_address">Address<span class="text-danger">*</span></label>
                                            <input type="text" name="pickup_address" id="pickup_address" placeholder="Enter address"class="form-control" value="{{ old('pickup_address', $parcel->pickup_address) }}">
                                            @error('pickup_address')
                                                <span class="text-danger mt-2">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                {{-- sending to --}}
                                <div class="col-md-6">
                                    <div class="border text-center p-2 mb-2 align-items-center">
                                        <h3 class="m-0 p-0">{{ __('parcel.sending_to') }}</h3>
                                    </div>
                                    <div class="row">
                                        <div class="form-group">
                                            <label for="to_state_id">{{ __('parcel.province_state') }} <span class="text-danger">*</span></label>
                                            <select style="width: 100%" id="to_state_id" class="form-control select2" name="to_state_id" data-url="{{ route('get.province.city') }}">
                                                <option value="">{{ __('menus.select') }} {{ __('parcel.province_state') }}</option>
                                                @foreach ($provinces as $province)
                                                    <option value="{{ $province->id }}" @selected(old('to_state_id', $parcel->to_state_id) == $province->id)>{{ $province->name }}({{ $province->province_code }})</option>
                                                @endforeach
                                            </select>
                                            @error('to_state_id')
                                                <span class="text-danger mt-2">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <input type="hidden" name="to_account_type" id="to_account_type" value="1">
                                        <div class="form-group">
                                            <nav class="my-0">
                                            <div class="nav nav-pills location-tabs to_point_button" id="nav-tab" role="tablist">
                                                    <button class="nav-link @if (old('to_point_type', $parcel->to_point_type) == 1 || (!old('to_point_type', $parcel->to_point_type) && !request('to_point_type', $parcel->to_point_type))) active @endif"  data-type="1" id="nav-delivery-point-tab"
                                                        data-bs-toggle="tab" data-bs-target="#nav-delivery-point" type="button" role="tab" aria-controls="nav-delivery-point"
                                                        aria-selected="true">
                                                        {{ __('parcel.delivery_point') }}
                                                    </button>
                                                    <input type="hidden" id="to_point" value="1"name="to_point_type">
                                                </div>
                                            </nav>
                                        </div>
                                        <div class="form-group toOnlyShowDoor">
                                            <label for="to_city_id">{{ __('parcel.city') }} <span class="text-danger">*</span></label>
                                            <select style="width: 100%" id="to_city_id" class="form-control select2" data-url="{{ route('city.wise.portal_code') }}" name="to_city_id">
                                                <option value="">{{ __('menus.select') }} {{ __('parcel.city') }}</option>
                                                @foreach ($cities as $city)
                                                    @if (old('to_state_id', $parcel->to_state_id) == $city->province_id)
                                                        <option value="{{ $city->id }}" @selected(old('to_city_id', $parcel->to_city_id) == $city->id)>{{ $city->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            @error('to_city_id')
                                                <span class="text-danger mt-2">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group toOnlyShowDoor">
                                            <label for="to_portal_code">{{ __('parcel.portal_code') }} <span class="text-danger">*</span></label>
                                            <input type="text" name="to_portal_code" id="to_portal_code" placeholder="{{ __('parcel.portal_code') }}" class="form-control" value="{{ old('to_portal_code',@$parcel->to_portal_code) }}">
                                            @error('to_portal_code')
                                                <span class="text-danger mt-2">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="border text-center p-2 mb-2 align-items-center">
                                        <h3 class="m-0 p-0">Recipient</h3>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="col-md-12 toAccountType d-block">
                                            <div class="form-group">
                                                <label for="to_merchant_id">{{ __('parcel.recipient') }}</label>
                                                <span class="text-danger">*</span>
                                                <select style="width: 100%" id="to_merchant_id" name="to_merchant_id" class="form-control select2" data-url="{{ route('parcel.merchant.shops') }}">
                                                    <option value=""> {{ __('menus.select') }}  {{ __('parcel.recipient') }} </option>
                                                    @foreach($merchants as $key => $to_merchant)
                                                            @if ($parcel->merchant_id == $to_merchant->id)
                                                                @php
                                                                    continue;
                                                                @endphp
                                                            @endif
                                                        @if(old('to_state_id', $parcel->to_state_id) == $to_merchant->user->province_id)
                                                            <option value="{{ @$to_merchant->id }}" @selected(old('to_merchant_id', $parcel->to_merchant_id) == $to_merchant->id)> {{ @$to_merchant->business_name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                @error('to_merchant_id')
                                                    <small class="text-danger mt-2">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12 toCustomerType d-none">
                                            <div class="form-group">
                                                <label for="customer_id">{{ __('parcel.customer') }}</label>
                                                <span class="text-danger">*</span>
                                                <select style="width: 100%" id="customer_id" name="customer_id" class="form-control select2" data-url="{{ route('parcel.customer.info') }}">
                                                    <option value=""> {{ __('menus.select') }}  {{ __('parcel.customer') }}</option>
                                                    @foreach($customers as $key => $customer)
                                                        @if(old('to_state_id',$parcel->to_state_id) == $customer->province_id)
                                                            <option value="{{ $customer->id }}" @selected(old('customer_id', $parcel->customer_id) == $customer->id)> {{ $customer->name }} ({{ $customer->phone_number }})</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                @error('customer_id')
                                                    <small class="text-danger mt-2">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12 d-none">
                                            <div class="form-group">
                                                <label for="shopID">{{ __('parcel.shop') }}<span class="text-danger">*</span></label>
                                                <select style="width: 100%" id="to_shopID" class="form-control select2" name="shop_id" data-url="{{ route('parcel.merchant.shops') }}"></select>
                                                @error('shop_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12" id="to_company_name_field" style="display: none">
                                            <div class="form-group">
                                                <label for="customer_company_name">{{ __('levels.company_name') }}</label>
                                                <input id="customer_company_name" type="text" name="customer_company_name" placeholder="{{ __('placeholder.enter_company_name') }}"
                                                    class="form-control" value="{{ old('customer_company_name', $parcel->customer_company_name) }}">
                                                @error('customer_company_name')
                                                    <small class="text-danger mt-2">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="customer_first_name">{{ __('levels.first_name') }} <span class="text-danger">*</span></label>
                                                <input id="customer_first_name" type="text" name="customer_first_name" placeholder="{{ __('placeholder.Enter_first_name') }}"
                                                    autocomplete="off" class="form-control" value="{{ old('customer_first_name', $parcel->customer_first_name) }}">
                                                @error('customer_first_name')
                                                    <small class="text-danger mt-2">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="customer_last_name">{{ __('levels.last_name') }} <span class="text-danger">*</span></label>
                                                <input id="customer_last_name" type="text" name="customer_last_name" placeholder="{{ __('placeholder.Enter_last_name') }}"
                                                    autocomplete="off" class="form-control" value="{{ old('customer_last_name', $parcel->customer_last_name) }}">
                                                @error('customer_last_name')
                                                    <small class="text-danger mt-2">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="customer_phone">{{ __('parcel.phone_number') }}</label>
                                                <span class="text-danger">*</span>
                                                <input id="customer_phone" type="text" name="customer_phone" placeholder="{{ __('placeholder.enter_phone_number') }}" autocomplete="off" class="form-control"
                                                    value="{{ old('customer_phone', $parcel->customer_phone) }}">
                                                @error('customer_phone')
                                                    <small class="text-danger mt-2">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="receiver_email">{{ __('levels.email') }}</label>
                                                <input id="receiver_email" type="text" name="receiver_email" placeholder="{{ __('parcel.receiver_email') }}" autocomplete="off" class="form-control"
                                                    value="{{ old('receiver_email', $parcel->receiver_email) }}">
                                                @error('receiver_email')
                                                    <small class="text-danger mt-2">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="customer_address">Address<span class="text-danger">*</span></label>
                                                <input type="text" name="customer_address" id="customer_address" placeholder="Enter address"class="form-control" value="{{ old('customer_address', $parcel->customer_address) }}">
                                                @error('customer_address')
                                                    <span class="text-danger mt-2">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="pickup_location">
                                            <span class="from_location_heading">Pickup Point</span>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="hidden" id="pickup_lat" name="pickup_lat" required="" value="{{ old('pickup_lat', $parcel->pickup_lat) }}">
                                        <input type="hidden" id="pickup_long" name="pickup_long" required="" value="{{ old('pickup_long', $parcel->pickup_long) }}">
                                        <div class="main-search-input-item location location-search">
                                            <div id="autocomplete-container" class="form-group random-search">
                                                <input id="autocomplete-input" type="text" name="pickup_location" class="recipe-search2 mb-2 form-control" value="{{ old('pickup_location', $parcel->pickup_location) }}" placeholder="Enter pickup point">
                                                <a href="javascript:void(0)" class="submit-btn btn current-location" id="locationIcon" onclick="getLocation()">
                                                    <i class="fa fa-crosshairs"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="googleMap" class="custom-map d-none"></div>
                                    @error('pickup_location')
                                        <span class="text-danger mt-2">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="drop_location">
                                            <span class="to_location_heading">Delivery Point</span>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="hidden" id="drop_latitude" name="drop_latitude" required="" value="{{ old('drop_latitude', $parcel->drop_latitude) }}">
                                        <input type="hidden" id="drop_longitude" name="drop_longitude" required="" value="{{ old('drop_longitude', $parcel->drop_longitude) }}">
                                        <div class="main-search-input-item location location-search">
                                            <div id="autocomplete-container" class="form-group random-search">
                                                <input id="autocomplete" type="text" name="drop_location" class="form-control mb-3" value="{{ old('drop_location', $parcel->drop_location) }}"  placeholder="{{ __('parcel.delivery_location_heading') }}" required="">
                                            </div>
                                        </div>
                                    </div>
                                    <div id="googleMapTwo" class="custom-map d-none"></div>
                                    @error('drop_location')
                                        <span class="text-danger mt-2">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-sm-12 mt-2 d-none">
                                    <div id="mapDirection" class="custom-map"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 mt-3 pb-3 cbm-all">
                                    <div class="cbm-box cbm-main mb-3 pt-3">
                                        <h4 class="text-center my-2 d-none">{{ __('parcel.cbm_formula_calculation') }}</h4>
                                        <div class="row">
                                            <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="package_type_id">{{ __('parcel.package_type') }} <span class="text-danger">*</span></label>
                                                    <select id="package_type_id" name="package_type_id" class="form-control w-100 select2">
                                                        <option value="">{{ __('levels.select') }} {{ __('parcel.package_type') }}</option>
                                                        <option value="1" @selected(old('package_type_id',@$parcel->cbm_details['package_type_id']) == 1)>{{ __('parcel.courier_document') }}</option>
                                                        <option value="2" @selected(old('package_type_id',@$parcel->cbm_details['package_type_id']) == 2)>{{ __('parcel.parcel_type') }}</option>
                                                    </select>
                                                    @error('package_type_id')
                                                        <span class="text-danger mt-2">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class=" col-md-6  col-lg-3 col-xl-4  col-sm-12 mb-2">
                                                <label for="quantity">{{ __('parcel.quantity') }} <span class="text-danger">*</span></label>
                                                <div class="form-group">
                                                    <input id="quantity" type="number" name="quantity" placeholder="{{ __('parcel.quantity') }}" autocomplete="off" class="form-control"
                                                        value="{{ old('quantity', @$parcel->cbm_details['quantity']) }}">
                                                </div>
                                                @error('quantity')
                                                    <small class="text-danger mt-2">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12 mb-2 parcelTypeId {{ old('package_type_id',@$parcel->cbm_details['package_type_id']) == 2 ? 'd-block' : 'd-none' }}">
                                                <label for="local_weight">{{ __('parcel.weight') }} <span class="text-danger">*</span></label>
                                                <div class="form-group">
                                                    <input id="local_weight" type="number" step="any"
                                                        name="local_weight"
                                                        placeholder="{{ __('parcel.weight') }}" autocomplete="off"
                                                        class="form-control"
                                                        value="{{ old('local_weight', @$parcel->cbm_details['local_weight']) }}">
                                                </div>
                                                @error('local_weight')
                                                    <small class="text-danger mt-2">{{ $message }}</small>
                                                @enderror
                                            </div>
                                            @if (SettingHelper('rush_hour_service_status') == \App\Enums\Status::ACTIVE)
                                                <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12">
                                                    <div class="form-group mt-4">
                                                        <div class="preview-block">
                                                            <div class="custom-control custom-checkbox">
                                                                <input type="checkbox"
                                                                    class="custom-control-input rush_hour_service"
                                                                    id="rush_hour_service"
                                                                    data-amount="{{ SettingHelper('rush_hour_service_charge') }}"
                                                                    data-outside-amount="{{ SettingHelper('rush_hour_service_outside_charge') }}"
                                                                    data-inside-distance="{{ settings()->inside_city_distance }}"
                                                                    name="rush_hour_service" onclick="rushHourServiceCheck(this);"
                                                                    @if (isset($parcel->cbm_details['rush_hour_service']) && $parcel->cbm_details['rush_hour_service'] != null) checked @endif
                                                                    value="{{ @$parcel->cbm_details['rush_hour_service'] }}">
                                                                <label class="custom-control-label" for="rush_hour_service">{{ __('parcel.rush_hour_service') }}</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12">
                                                <div class="form-group mt-4">
                                                    <div class="preview-block">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input extra_cost" id="extra_cost"
                                                                name="extra_cost" onclick="extraCostMainCheck(this);" @if($parcel->cbm_details['extra_cost']) checked @endif>
                                                            <label class="custom-control-label" for="extra_cost">{{ __('parcel.extra_cost') }}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12 extraCostCheck1 {{ old('extra_cost',@$parcel->cbm_details['extra_cost']) ? 'd-block' : 'd-none' }}">
                                                <div class="form-group">
                                                    <label for="extra_cost_amount">{{ __('parcel.extra_cost_amount') }}<span class="text-danger">*</span></label>
                                                    <input type="number" name="extra_cost_amount" value="{{ old('extra_cost_amount',@$parcel->cbm_details['extra_cost_amount']) }}" id="extra_cost_amount" class="form-control extra_cost_amount" placeholder="{{ __('parcel.extra_cost_amount') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12 extraCostCheck1 {{ old('extra_cost',@$parcel->cbm_details['extra_cost']) ? 'd-block' : 'd-none' }}">
                                                <div class="form-group">
                                                    <label for="extra_cost_description">{{ __('parcel.extra_cost_description') }}</label>
                                                    <input type="text" name="extra_cost_description" value="{{ old('extra_cost_description',@$parcel->cbm_details['extra_cost_description']) }}" id="extra_cost_description" class="form-control extra_cost_description" placeholder="{{ __('parcel.extra_cost_description') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12">
                                                <div class="form-group">
                                                    <label for="packaging_id">{{ __('parcel.packaging') }}</label>
                                                    <select id="packaging_id" class="form-control select2 packaging_id select2" name="packaging_id" onchange="packagingServiceCheck(this);">
                                                        <option value=""> {{ __('menus.select') }} {{ __('menus.packaging') }}</option>
                                                        @foreach ($packagings as $packaging)
                                                            <option data-packagingamount="{{ $packaging->price }}" value="{{ $packaging->id }}" @selected(old('packaging_id',@$parcel->cbm_details['packaging_id']) == $packaging->id)>
                                                                {{ $packaging->name }} ( {{ number_format($packaging->price, 2) }} {{ settings()->currency }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('packaging_id')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12 mb-2">
                                                <div class="form-group">
                                                    <label for="content_parcel">{{ __('parcel.content_parcel') }}</label>
                                                    <input id="content_parcel" type="text" name="content_parcel" placeholder="{{ __('parcel.content_parcel') }}" autocomplete="off" class="form-control" value="{{ old('content_parcel',@$parcel->cbm_details['content_parcel']) }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 text-end">
                                                <button type="button" class="cloneMainParcelItem text-white btn btn-info mt-2">
                                                    <i class="fa fa-clone me-2"></i> {{ __('parcel.copy_this_parcel') }}
                                                </button>
                                            </div>
                                            <div class="col-md-12 col-sm-12 cbm-input-group mb-2">
                                                <div class="d-flex align-items-end justify-content-center h-100">
                                                    <input type="hidden" id="main_total_cbm" name="main_total_cbm" value="{{ @$parcel->cbm_details['total_cbm'] }}" />
                                                    <input type="hidden" id="main_total_weight" name="main_total_weight" value="{{ @$parcel->cbm_details['total_weight'] }}" />
                                                    <input type="hidden" id="total_weight" name="total_weight" value="{{ $parcel->total_weight }}" />
                                                    <input type="hidden" id="total_cbm" value="{{ old('total_cbm', $parcel->total_cubic_meters) }}" name="total_cbm" />
                                                    <input type="hidden" id="parcel_item_status" name="parcel_item_status" value="{{ old('parcel_item_status', @$parcel->cbm_details['parcel_item_status']) }}">
                                                    <input type="hidden" id="shipping_fee_status" name="shipping_fee_status" value="{{ old('shipping_fee_status', @$parcel->cbm_details['shipping_fee_status']) }}">
                                                    <input type="hidden" id="package_total_row" name="package_total_row" value="0">
                                                    <input type="hidden" id="total_cbm_weight" name="total_valumetric_weight" value="{{ $parcel->total_valumetric_weight?? 0 }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cbm-row">
                                        @if ($parcel->items && $parcel->items->count() > 0)
                                            @foreach ($parcel->items as $key => $item)
                                                @include('backend.parcel.edit_parcel_item', [
                                                    'item' => $item,
                                                    'key' => $key,
                                                    'parcelCategories' => $parcelCategories,
                                                ])
                                            @endforeach
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <a href="javascript:void(0)"
                                            class="text-white font-weight-bold text-right btn btn-success" id="add_item">
                                            <i class="fa fa-plus me-2"></i> {{ __('parcel.add_another_parcel') }}
                                        </a>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="col-sm-12 mb-2">
                                            <div class="form-group">
                                                <label for="who_pays_either">{{ __('parcel.who_pays_either') }} <span class="text-danger">*</span></label>
                                                <select name="who_pays_either" class="form-control" id="who_pays_either" style="width: 100% !important" required>
                                                    <option value="">{{ __('levels.select') }} {{ __('parcel.who_pays_either') }}</option>
                                                    <option value="{{ App\Enums\WhoPays::RECIPIENT }}" @selected(old('who_pays_either', @$parcel->who_pays_either) == App\Enums\WhoPays::RECIPIENT)>{{ __('parcel.recipient') }}</option>
                                                    <option value="{{ App\Enums\WhoPays::SENDER }}" @selected(old('who_pays_either', @$parcel->who_pays_either) == App\Enums\WhoPays::SENDER)>{{ __('parcel.sender') }}</option>
                                                </select>
                                                @error('who_pays_either')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-12 col-md-6">
                                    <label for="special_discount">{{ __('parcel.special_discount') }} ({{ __('parcel.fixed') }})</label>
                                    <span class="text-danger">*</span>
                                    <input id="special_discount" type="text" name="special_discount" placeholder="{{ __('levels.special_discount') }}" autocomplete="off" class="form-control" value="{{ old('special_discount', $parcel->special_discount) }}" required="">
                                    @error('special_discount')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group col-12 col-md-6">
                                    <label for="note">{{ __('parcel.note') }}</label>
                                    <textarea id="note" name="note" class="form-control" rows="5">{{ old('note', $parcel->note) }}</textarea>
                                    @error('note')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                                <input type="hidden" id="merchantVat" name="vat_tex" value="{{ $parcel->vat ?? 0 }}" />
                                <input type="hidden" id="merchantCodCharge" name="cod_charge" value="{{ $parcel->cod_charge ?? 0 }}" />
                                <input type="hidden" id="chargeDetails" name="chargeDetails" value="" />
                                <input type="hidden" name="distance_km" class="distance_km" value="{{ old('distance_km', $parcel->distance_km) }}" />
                                <input type="hidden" name="merchant_discount" class="merchant_discount" value="{{ old('merchant_discount', $parcel->discount) }}" />
                                <input type="hidden" name="merchant_discount_amount" class="merchant_discount_amount" value="{{ old('merchant_discount_amount', $parcel->discount_amount) }}" />
                                <input type="hidden" id="discount_eligible" value="true" />
                            </div>
                            <div class="col-md-12 mt-2">
                                <div class="row pt-1">
                                    <div class="col-12 col-sm-4 p-0">
                                        <div class="form-group">
                                            <div class="preview-block">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="radio" class="custom-control-input" id="pickup_type" name="pick_type" value="1" @if (old('pick_type', $parcel->pick_type) != 3 && old('pick_type', $parcel->pick_type) != 2 && old('pick_type', $parcel->pick_type) == 1) checked @endif>
                                                    <label class="custom-control-label" for="pickup_type">{{ __('parcel.today') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group">
                                            <div class="preview-block">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="radio" class="custom-control-input" id="tomorrow" name="pick_type" value="2" @if (old('pick_type', $parcel->pick_type) == 2) checked @endif>
                                                    <label class="custom-control-label" for="tomorrow">{{ __('parcel.tomorrow') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group">
                                            <div class="preview-block">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="radio" class="custom-control-input" id="schedule" name="pick_type" value="3" @if (old('pick_type', $parcel->pick_type) == 3) checked @endif>
                                                    <input type="hidden" class="scheduled" id="scheduled"
                                                            data-amount="{{ SettingHelper('scheduled_service_charge') }}"
                                                            data-outside-amount="{{ SettingHelper('scheduled_service_outside_charge') }}"
                                                            data-inside-distance="{{ settings()->inside_city_distance }}"
                                                            name="scheduled_amount" value="{{ old('scheduled_amount',0) }}">
                                                    <label class="custom-control-label" for="schedule">{{ __('parcel.schedule') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12">
                                        @error('pick_type')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-sm-6 scheduledField @if (old('pick_type',$parcel->pick_type) != 3) d-none @endif">
                                        <div class="form-group">
                                            <label for="pickup_date">{{ __('parcel.pickup_date') }}<span class="text-danger">*</span></label>
                                            <input type="datetime-local" name="pickup_date" class="form-control" value="{{ old('pickup_date',$parcel->pickup_date) }}">
                                            @error('pickup_date')
                                                <p class="text-danger mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-6 scheduledField @if (old('pick_type',$parcel->pick_type) != 3) d-none @endif">
                                        <div class="form-group">
                                            <label for="delivery_date">{{ __('parcel.delivery_date') }}<span class="text-danger">*</span></label>
                                            <input type="datetime-local" name="delivery_date" class="form-control" value="{{ old('delivery_date',$parcel->delivery_date) }}">
                                            @error('delivery_date')
                                                <p class="text-danger mt-2">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-12 p-0">
                                        <div class="form-group">
                                            <div class="preview-block">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="policy" name="policy" @if (old('policy', $parcel->policy) == 1) checked @endif>
                                                    <label class="custom-control-label" for="policy"><span>I agree to <a class="text-primary">{{ settings()->name }}</a> Privacy Policy &amp; Terms.</span></label>
                                                </div>
                                            </div>
                                            @error('policy')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12  d-flex justify-content-end">
                                    <a href="{{ route('parcel.index') }}" class="btn btn-space btn-secondary">
                                        {{ __('levels.cancel') }}
                                    </a>
                                    <button type="submit" class="btn btn-space btn-primary">
                                        {{ __('levels.save_change') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-12 col-lg-12 col-xl-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                        <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('parcel.charge_details') }}</h4>
                    </div>
                    <div class="card-body">
                        <!-- <h2 class="pageheader-title">{{ __('parcel.charge_details') }}</h2> -->
                        <ul class="list-group">
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('levels.title') }}</span>
                                <span class="float-right">{{ __('levels.amount') }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">Volumetric Weight</span>
                                <span class="float-right"><span id="total_valumetric_weight">{{ $parcel->total_valumetric_weight?? 0 }} kg</span></span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('parcel.side_type') }}</span>
                                <span class="float-right"><span id="sideTypeText"></span></span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('parcel.distance') }}</span>
                                <span class="float-right"><span id="distance_km">{{ $parcel->distance_km ?? 0 }}</span> {{ __('parcel.km') }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('parcel.Delivery_Charge') }}</span>
                                <span class="float-right" id="deliveryChargeAmount">{{ $parcel->delivery_charge + $parcel->special_discount ?? '0.00' }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('parcel.special_discount') }}</span>
                                <span class="float-right"> - <span id="special_discount_amount">{{ $parcel->special_discount ?? '0.00' }}</span></span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('parcel.sub_total') }}</span>
                                <span class="float-right font-weight-bold"id="sub_total">{{ $parcel->delivery_charge }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item d-none">
                                <span class="float-left font-weight-bold">{{ __('reports.COD_Charge') }}</span>
                                <span class="float-right" id="codChargeAmount">{{ $parcel->cod_amount ?? '0.00' }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item hideShowLiquidFragile">
                                <span class="float-left font-weight-bold">{{ __('parcel.Liquid/Fragile_Charge') }}</span>
                                <span class="float-right" id="liquidFragileAmount">{{ $parcel->liquid_fragile_amount ?? '0.00' }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item rushHourService">
                                <span class="float-left font-weight-bold">{{ __('parcel.rush_hour_service') }}</span>
                                <span class="float-right" id="rushHourServiceAmount">{{ $parcel->rush_hour_amount ?? '0.00' }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item scheduledService">
                                <span class="float-left font-weight-bold">{{ __('parcel.scheduled_amount') }}</span>
                                <span class="float-right" id="scheduledServiceAmount">{{ $parcel->scheduled_amount ?? '0.00' }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item extraCostCheckService">
                                <span class="float-left font-weight-bold">{{ __('parcel.extra_cost') }}</span>
                                <span class="float-right" id="extraCostAmount">{{ $parcel->total_extra_cost ?? '0.00' }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item" id="packagingShow">
                                <span class="float-left font-weight-bold">{{ __('parcel.packaging_charge') }}</span>
                                <span class="float-right" id="packagingAmount">{{ $parcel->packaging_amount ?? '0.00' }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('parcel.Total_Charge') }}</span>
                                <span class="float-right" id="totalDeliveryChargeAmount">{{ $parcel->total_delivery_amount ?? '0.00' }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('parcel.discount') }} (-)</span>
                                <span class="float-right" id="merchant_discount">{{ $parcel->discount_amount }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('parcel.Vat') }}</span>
                                <span class="float-right" id="VatAmount">{{ $parcel->vat_amount ?? '0.00' }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('parcel.total_shipping_fee') }}</span>
                                <span class="float-right" id="netPayable">{{ $parcel->total_delivery_amount + $parcel->vat_amount - $parcel->discount_amount }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('parcel.Current_payable') }}</span>
                                <span class="float-right" id="currentPayable">{{ $parcel->current_payable ?? '0.00' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
        .disabledbutton {
            pointer-events: none;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" />
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        var deliverChargeUnitParcelUrl = '{{ route('parcel.deliveryCharge.unitParcel.get') }}';
        var deliverChargeUrl = '{{ route('parcel.deliveryCharge.get') }}';
        var merchantUrl = '{{ route('parcel.merchant.get') }}';
        var category_id = '{{ $parcel->category_id }}';
        var packaging_id = '{{ $parcel->packaging_id }}';
        var liquid_fragile_amount = '{{ $parcel->liquid_fragile_amount }}';
        var receiverSuggestion = '{{ route('get.receiver.suggestions') }}';
        $(document).ready(function () {
            $(".select2").select2();
        });
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        @if (!empty($errors->all()))
            @foreach ($errors->all() as $error)
                toastr.error("{{$error}}")
            @endforeach
        @endif
    </script>
    @include('backend.parcel.location.location_url')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script> 
    <script src="{{ static_asset('backend/js/parcel/edit.js') }}"></script>
@endpush
