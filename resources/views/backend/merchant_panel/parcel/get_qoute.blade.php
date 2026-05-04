<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Document</title>
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

        .location-tabs .nav-link.active {
            background: var(--warningcolor) !important;
        }

        .current-location {
            margin-right: 50px;
            margin-top: 5px;
            color: var(--bs-primary) !important;
        }

        #bookingDate {
            display: none;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ static_asset('backend') }}/vendor/bootstrap-five/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css">
    <link href="{{ static_asset('backend') }}/vendor/fonts/circular-std/style.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ static_asset('backend') }}/libs/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css"
        integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="{{ static_asset('backend') }}/vendor/fonts/fontawesome/css/fontawesome-all.css">
    <link rel="stylesheet" href="{{ static_asset('backend') }}/vendor/charts/chartist-bundle/chartist.css">
    <link rel="stylesheet" href="{{ static_asset('backend') }}/vendor/charts/morris-bundle/morris.css">
    <link rel="stylesheet"
        href="{{ static_asset('backend') }}/vendor/fonts/material-design-iconic-font/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="{{ static_asset('backend') }}/vendor/charts/c3charts/c3.css">
    <link rel="stylesheet" href="{{ static_asset('backend') }}/vendor/fonts/flag-icon-css/flag-icon.min.css">
    <link rel="stylesheet" href="{{ static_asset('backend') }}/libs/css/datepicker.min.css">
    <link rel="stylesheet" href="{{ static_asset('backend') }}/libs/css/custom.css">
    <link rel="stylesheet" href="{{ static_asset('backend') }}/css/custom.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.5.1/css/flag-icons.min.css" />
    <script src="{{ static_asset('backend/vendor/pace-progress-bar/pace.min.js') }}"></script>
    <link rel="stylesheet" href="{{ static_asset('backend/vendor/pace-progress-bar/pace-theme-default.min.css') }}">
    <link rel="stylesheet" href="{{ static_asset('backend/vendor') }}/toastr/toastr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .card {
            border: none !important;
            box-shadow: 0px 0px 5px #00000024;
        }

        .modal .list-group-item {
            padding: 10px 20px !important;
        }
    </style>
</head>

<body style="background-color: white!important;">

    {{-- <div class="row">
        <div class="col-md-12"> --}}
    <div>
        <div class="col-md-12 pt-3 pb-2">
            <div class="card">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="card-body">
                    {{-- <h2 class="pageheader-title">{{ __('parcel.create_parcel') }}</h2> --}}
                    <form action="{{ route('merchant-panel.parcel.store') }}" method="POST" id="basicform">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="border text-center p-2 mb-2 align-items-center">
                                    <h3 class="m-0 p-0">{{ __('parcel.sending_from') }}</h3>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="from_state_id">{{ __('parcel.province_state') }} <span
                                                    class="text-danger">*</span></label>
                                            <select name="from_state_id" id="from_state_id"
                                                data-url="{{ route('get.province.city') }}"
                                                class="form-control select2" style="width: 100%">
                                                <option value="">{{ __('menus.select') }}
                                                    {{ __('parcel.province_state') }}</option>
                                                @foreach ($provinces as $province)
                                                    <option value="{{ $province->id }}" @selected(old('from_state_id') == $province->id)>
                                                        {{ $province->name }}({{ $province->province_code }})</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" id="getProvinceAccountTypeWiseMerchant"
                                                data-url="{{ route('getqoute.get.province.account_type.wise.merchant') }}" />
                                            <input type="hidden" id="urlCustomer"
                                                data-url="{{ route('get.merchant.customer') }}" />
                                            @error('from_state_id')
                                                <span class="text-danger mt-2">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <nav class="my-2">
                                            <div class="nav nav-pills location-tabs from_point_button" id="nav-tab"
                                                role="tablist">
                                                <button class="nav-link  active" data-type="1" id="nav-home-tab"
                                                    data-bs-toggle="tab" data-bs-target="#nav-home" type="button"
                                                    role="tab" aria-controls="nav-home"
                                                    aria-selected="true">{{ __('parcel.pickup_point') }}</button>
                                                <button class="nav-link" data-type="2" id="nav-profile-tab"
                                                    data-bs-toggle="tab" data-bs-target="#nav-profile" type="button"
                                                    role="tab" aria-controls="nav-profile"
                                                    aria-selected="false">{{ settings()->name }}
                                                    {{ __('parcel.drop_off_point') }}</button>
                                                <input type="hidden" id="from_point" value="1"
                                                    name="from_point_type">
                                            </div>
                                        </nav>
                                    </div>
                                    <div
                                        class="col-md-12 fromPointButton {{ old('from_point_type') == 2 ? 'd-block' : 'd-none' }}">
                                        <div class="form-group">
                                            <label for="from_hub_id">{{ __('parcel.hub') }} <span
                                                    class="text-danger">*</span></label>
                                            <select style="width: 100%" id="from_hub_id" onchange="getFromHubInfo()"
                                                class="form-control select2" name="from_hub_id">
                                                <option value="">{{ __('menus.select') }}
                                                    {{ __('parcel.hub') }}
                                                </option>
                                            </select>
                                            @error('from_hub_id')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <a class="btn btn-primary" data-toggle="collapse" href="#collapseExample"
                                                role="button" aria-expanded="false" aria-controls="collapseExample">
                                                {{ __('parcel.show_hub_information') }}
                                            </a>
                                        </div>
                                        <div class="form-group collapse" id="collapseExample">
                                            <div class="card card-body">
                                                <p class="m-0 p-1"><strong>{{ __('parcel.hub') }}:</strong> <span
                                                        id="from_hub_name"></span></p>
                                                <p class="m-0 p-1"><strong>{{ __('parcel.phone_number') }}:</strong>
                                                    <span id="from_hub_phone_number"></span>
                                                </p>
                                                <p class="m-0 p-1">
                                                    <strong>{{ __('parcel.whatsapp_number') }}:</strong>
                                                    <span id="hub_from_whatsapp_number"></span>
                                                </p>
                                                <p class="m-0 p-1"><strong>{{ __('levels.email') }}:</strong> <span
                                                        id="from_hub_email"></span></p>
                                                <p class="m-0 p-1"><strong>{{ __('levels.address') }}:</strong> <span
                                                        id="from_hub_residential_address"></span></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-12 fromOnlyShowDoor {{ old('from_point_type') == 2 ? 'd-none' : 'd-block' }}">
                                        <div class="form-group">
                                            <label for="from_city_id">{{ __('parcel.city') }} <span
                                                    class="text-danger">*</span></label>
                                            <select name="from_city_id" id="from_city_id"
                                                class="form-control select2"
                                                data-url="{{ route('city.wise.portal_code') }}" style="width: 100%">
                                                <option value="">{{ __('menus.select') }}
                                                    {{ __('parcel.city') }}</option>
                                                @foreach ($cities as $city)
                                                    @if (old('from_state_id') == $city->province_id)
                                                        <option value="{{ $city->id }}"
                                                            @selected(old('from_city_id') == $city->id)>{{ $city->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            @error('from_city_id')
                                                <span class="text-danger mt-2">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div
                                        class="col-md-12 fromOnlyShowDoor {{ old('from_point_type') == 2 ? 'd-none' : 'd-block' }}">
                                        <div class="form-group">
                                            <label for="from_portal_code">{{ __('parcel.portal_code') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="from_portal_code" id="from_portal_code"
                                                placeholder="{{ __('parcel.portal_code') }}"class="form-control"
                                                value="{{ old('from_portal_code') }}">
                                            @error('from_portal_code')
                                                <span class="text-danger mt-2">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border text-center p-2 mb-2 align-items-center">
                                    <h3 class="m-0 p-0">{{ __('parcel.sending_to') }}</h3>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="to_state_id">{{ __('parcel.province_state') }} <span
                                                    class="text-danger">*</span></label>
                                            <select style="width: 100%" id="to_state_id" class="form-control select2"
                                                data-url="{{ route('get.province.city') }}" name="to_state_id">
                                                <option value="">{{ __('menus.select') }}
                                                    {{ __('parcel.province_state') }}</option>
                                                @foreach ($provinces as $province)
                                                    <option value="{{ $province->id }}" @selected(old('to_state_id') == $province->id)>
                                                        {{ $province->name }}({{ $province->province_code }})</option>
                                                @endforeach
                                            </select>
                                            @error('to_state_id')
                                                <span class="text-danger mt-2">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <nav class="my-2">
                                            <div class="nav nav-pills location-tabs to_point_button" id="nav-tab"
                                                role="tablist">
                                                <button class="nav-link  active" data-type="1"
                                                    id="nav-delivery-point-tab" data-bs-toggle="tab"
                                                    data-bs-target="#nav-delivery-point" type="button"
                                                    role="tab" aria-controls="nav-delivery-point"
                                                    aria-selected="true">{{ __('parcel.delivery_point') }}</button>
                                                <button class="nav-link" data-type="2" id="nav-drop-off-point-tab"
                                                    data-bs-toggle="tab" data-bs-target="#nav-drop-off-point"
                                                    type="button" role="tab" aria-controls="nav-drop-off-point"
                                                    aria-selected="false">{{ settings()->name }}
                                                    {{ __('parcel.drop_off_point') }}</button>
                                                <input type="hidden" id="to_point"
                                                    value="1"name="to_point_type">
                                            </div>
                                        </nav>
                                    </div>
                                    <div
                                        class="col-md-12 toPointButton {{ old('to_point_type') == 2 ? 'd-block' : 'd-none' }}">
                                        <div class="form-group">
                                            <label for="to_hub_id">{{ __('parcel.hub') }} <span
                                                    class="text-danger">*</span></label>
                                            <select style="width: 100%" id="to_hub_id" class="form-control select2"
                                                onchange="getToHubInfo()" name="to_hub_id">
                                                <option value=""> {{ __('menus.select') }}
                                                    {{ __('parcel.hub') }}</option>
                                            </select>
                                            @error('to_hub_id')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <button class="btn btn-primary" type="button" data-toggle="collapse"
                                                data-target="#collapseExample1" aria-expanded="false"
                                                aria-controls="collapseExample1">
                                                {{ __('parcel.show_hub_information') }}
                                            </button>
                                        </div>
                                        <div class="form-group collapse" id="collapseExample1">
                                            <div class="card card-body">
                                                <p class="m-0 p-1"><strong>{{ __('parcel.hub') }}:</strong> <span
                                                        id="to_hub_name"></span></p>
                                                <p class="m-0 p-1"><strong>{{ __('parcel.phone_number') }}:</strong>
                                                    <span id="to_hub_phone_number"></span>
                                                </p>
                                                <p class="m-0 p-1">
                                                    <strong>{{ __('parcel.whatsapp_number') }}:</strong> <span
                                                        id="to_whatsapp_number"></span>
                                                </p>
                                                <p class="m-0 p-1"><strong>{{ __('levels.email') }}:</strong> <span
                                                        id="to_hub_email"></span></p>
                                                <p class="m-0 p-1"><strong>{{ __('levels.address') }}:</strong> <span
                                                        id="to_hub_residential_address"></span></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="col-sm-12 toOnlyShowDoor {{ old('to_point_type') == 2 ? 'd-none' : 'd-block' }}">
                                        <div class="form-group">
                                            <label for="to_city_id">{{ __('parcel.city') }} <span
                                                    class="text-danger">*</span></label>
                                            <select style="width: 100%" id="to_city_id" class="form-control select2"
                                                data-url="{{ route('city.wise.portal_code') }}" name="to_city_id">
                                                <option value="">{{ __('menus.select') }}
                                                    {{ __('parcel.city') }}</option>
                                                @foreach ($cities as $city)
                                                    @if (old('to_state_id') == $city->province_id)
                                                        <option value="{{ $city->id }}"
                                                            @selected(old('to_city_id') == $city->id)>{{ $city->name }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            @error('to_city_id')
                                                <span class="text-danger mt-2">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div
                                        class="col-sm-12 toOnlyShowDoor {{ old('to_point_type') == 2 ? 'd-none' : 'd-block' }}">
                                        <div class="form-group">
                                            <label for="to_portal_code">{{ __('parcel.portal_code') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="to_portal_code" id="to_portal_code"
                                                placeholder="{{ __('parcel.portal_code') }}" class="form-control"
                                                value="{{ old('to_portal_code') }}">
                                            @error('to_portal_code')
                                                <span class="text-danger mt-2">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-12">

                                        <div class="col-md-12 d-none">
                                            <div class="form-group" style="pointer-events: none">
                                                <label for="shipping_type">{{ __('parcel.shipping_type') }} (Your
                                                    Seleceted Result)<span class="text-danger">*</span></label>
                                                <select id="shipping_type" name="shipping_type"
                                                    class="form-control select2 @error('shipping_type') is-invalid @enderror">
                                                    <option selected disabled>{{ __('menus.select') }}
                                                        {{ __('parcel.shipping_type') }}</option>
                                                </select>
                                                @error('shipping_type')
                                                    <small class="text-danger mt-2">{{ $message }}</small>
                                                @enderror
                                            </div>
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
                                    <input type="hidden" id="pickup_lat" name="pickup_lat" required=""
                                        value="{{ old('pickup_lat', request()->pickup_lat) }}">
                                    <input type="hidden" id="pickup_long" name="pickup_long" required=""
                                        value="{{ old('pickup_long', request()->pickup_long) }}">
                                    <div class="main-search-input-item location location-search">
                                        <div id="autocomplete-container" class="form-group random-search">
                                            <input id="autocomplete-inputs" type="text" name="pickup_location"
                                                class="recipe-search2 form-control autocomplete-inputs"
                                                value="{{ old('pickup_location', request()->pickup_location) }}"
                                                placeholder="Enter pickup point">
                                            <a href="javascript:void(0)" class="submit-btn btn current-location"
                                                id="locationIcon" onclick="getLocation()">
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
                                    <input type="hidden" id="drop_latitude" name="drop_latitude" required=""
                                        value="{{ old('drop_latitude', request()->drop_lat) }}">
                                    <input type="hidden" id="drop_longitude" name="drop_longitude" required=""
                                        value="{{ old('drop_longitude', request()->drop_long) }}">
                                    <div class="main-search-input-item location location-search">
                                        <input id="autocomplete" type="text" name="drop_location"
                                            class="recipe-search2 form-control"
                                            value="{{ old('drop_location', request()->drop_location) }}"
                                            required="" placeholder="Enter Delivery point">
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
                                    <h4 class="text-center my-2 d-none">{{ __('parcel.cbm_formula_calculation') }}
                                    </h4>
                                    <div class="row">
                                        <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="package_type_id">{{ __('parcel.package_type') }} <span
                                                        class="text-danger">*</span></label>
                                                <select id="package_type_id" name="package_type_id"
                                                    class="form-control w-100 select2">
                                                    <option value="1" @selected(old('package_type_id') == 1)>
                                                        {{ __('parcel.courier_document') }}</option>
                                                    <option value="2" @selected(old('package_type_id') == 2)>
                                                        {{ __('parcel.parcel_type') }}</option>
                                                </select>
                                                @error('package_type_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6  col-lg-3 col-xl-4  col-sm-12 cbm-input-group">
                                            <label for="length">{{ __('parcel.length') }} <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input id="length" type="number" name="length"
                                                    placeholder="{{ __('parcel.length') }}" class="form-control"
                                                    value="{{ old('length') }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon2">
                                                        {{ __('parcel.cm') }}
                                                    </span>
                                                </div>
                                            </div>
                                            @error('length')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6  col-lg-3 col-xl-4  col-sm-12 cbm-input-group mb-2">
                                            <label for="width">{{ __('parcel.width') }} <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group ">
                                                <input id="width" type="number" name="width"
                                                    placeholder="{{ __('parcel.width') }}" class="form-control"
                                                    value="{{ old('width') }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="basic-addon2">
                                                        {{ __('parcel.cm') }}
                                                    </span>
                                                </div>
                                            </div>
                                            @error('width')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class=" col-md-6  col-lg-3 col-xl-4  col-sm-12 cbm-input-group mb-2">
                                            <label for="height">{{ __('parcel.height') }} <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group ">
                                                <input id="height" type="number" name="height"
                                                    placeholder="{{ __('parcel.height') }}" class="form-control"
                                                    value="{{ old('height') }}">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"
                                                        id="basic-addon2">{{ __('parcel.cm') }}</span>
                                                </div>
                                            </div>
                                            @error('height')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class=" col-md-6  col-lg-3 col-xl-4  col-sm-12 mb-2">
                                            <label for="local_weight">{{ __('parcel.weight') }} </label>
                                            <div class="form-group">
                                                <input id="local_weight" type="number" step="any"
                                                    name="local_weight" placeholder="{{ __('parcel.weight') }}"
                                                    class="form-control" value="{{ old('local_weight') }}">
                                            </div>
                                            @error('local_weight')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class=" col-md-6  col-lg-3 col-xl-4 col-sm-12 mb-2">
                                            <label for="quantity">{{ __('parcel.quantity') }} <span
                                                    class="text-danger">*</span></label>
                                            <div class="form-group">
                                                <input id="quantity" type="number" name="quantity"
                                                    placeholder="{{ __('parcel.quantity') }}" class="form-control"
                                                    value="{{ old('quantity', 1) }}">
                                            </div>
                                            @error('quantity')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12 parcelTypeId d-none">
                                            <div class="form-group">
                                                <label for="main_category_id">{{ __('parcel.category') }} <span
                                                        class="text-danger">*</span></label>
                                                <select id="main_category_id" name="main_category_id"
                                                    class="form-control select2" style="width: 100% !important">
                                                    <option value="">{{ __('levels.select') }}
                                                        {{ __('parcel.category') }}</option>
                                                    @foreach ($parcelCategories as $parcelcategory)
                                                        <option value="{{ $parcelcategory->id }}"
                                                            @selected(old('main_category_id') == $parcelcategory->id)>{{ $parcelcategory->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('main_category_id')
                                                    <span class="text-danger mt-2">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        @if (SettingHelper('fragile_liquid_status') == \App\Enums\Status::ACTIVE)
                                            <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12 parcelTypeId d-none">
                                                <div class="form-group mt-4">
                                                    <div class="preview-block">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox"
                                                                class="custom-control-input fragile-liquid"
                                                                id="fragileLiquid"
                                                                data-amount="{{ SettingHelper('fragile_liquid_charge') }}"
                                                                data-outside-amount="{{ SettingHelper('fragile_liquid_outside_charge') }}"
                                                                data-inside-distance="{{ settings()->inside_city_distance }}"
                                                                name="fragileLiquid" onclick="processCheck(this);">
                                                            <label class="custom-control-label"
                                                                for="fragileLiquid">{{ __('parcel.liquid_fragile') }}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
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
                                                                name="rush_hour_service"
                                                                onclick="rushHourServiceCheck(this);" value="0">
                                                            <label class="custom-control-label"
                                                                for="rush_hour_service">{{ __('parcel.rush_hour_service') }}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12">
                                            <div class="form-group mt-4">
                                                <div class="preview-block">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input extra_cost"
                                                            id="extra_cost" name="extra_cost"
                                                            onclick="extraCostMainCheck(this);">
                                                        <label class="custom-control-label"
                                                            for="extra_cost">{{ __('parcel.extra_cost') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="col-md-6 col-lg-3 col-xl-4 col-sm-12 extraCostCheck1 {{ old('extra_cost') ? 'd-block' : 'd-none' }}">
                                            <div class="form-group">
                                                <label
                                                    for="extra_cost_amount">{{ __('parcel.extra_cost_amount') }}<span
                                                        class="text-danger">*</span></label>
                                                <input type="number" name="extra_cost_amount" id="extra_cost_amount"
                                                    class="form-control extra_cost_amount"
                                                    placeholder="{{ __('parcel.extra_cost_amount') }}">
                                            </div>
                                        </div>
                                        <div
                                            class="col-md-6 col-lg-3 col-xl-4 col-sm-12 extraCostCheck1 {{ old('extra_cost') ? 'd-block' : 'd-none' }}">
                                            <div class="form-group">
                                                <label
                                                    for="extra_cost_description">{{ __('parcel.extra_cost_description') }}</label>
                                                <input type="text" name="extra_cost_description"
                                                    id="extra_cost_description"
                                                    class="form-control extra_cost_description"
                                                    placeholder="{{ __('parcel.extra_cost_description') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12">
                                            <div class="form-group">
                                                <label for="packaging_id">{{ __('parcel.packaging') }}</label>
                                                <select id="packaging_id" class="form-control packaging_id select2"
                                                    name="packaging_id" onchange="packagingServiceCheck(this);"
                                                    style="width: 100% !important">
                                                    <option value=""> {{ __('menus.select') }}
                                                        {{ __('menus.packaging') }}</option>
                                                    @foreach ($packagings as $packaging)
                                                        <option data-packagingamount="{{ $packaging->price }}"
                                                            value="{{ $packaging->id }}"
                                                            {{ old('packaging_id') == $packaging->id ? 'selected' : '' }}>
                                                            {{ $packaging->name }} (
                                                            {{ number_format($packaging->price, 2) }}
                                                            {{ settings()->currency }})
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
                                                <input id="content_parcel" type="text" name="content_parcel"
                                                    placeholder="{{ __('parcel.content_parcel') }}"
                                                    class="form-control" value="{{ old('content_parcel') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 text-end">
                                            <button type="button"
                                                class="cloneMainParcelItem text-white btn btn-info mt-2">
                                                <i class="fa fa-clone me-2"></i> {{ __('parcel.copy_this_parcel') }}
                                            </button>
                                        </div>
                                        <div class="col-md-12 col-sm-12 cbm-input-group mb-2">
                                            <div class="d-flex align-items-end justify-content-center h-100">
                                                <input type="hidden" id="main_unit_parcel_service_cost"
                                                    name="main_unit_parcel_service_cost" value="0">
                                                <input type="hidden" id="main_total_cbm" name="main_total_cbm"
                                                    value="0" />
                                                <input type="hidden" id="main_total_weight" name="main_total_weight"
                                                    value="0" />
                                                <input type="hidden" id="total_weight" name="total_weight"
                                                    value="0" />
                                                <input type="hidden" id="total_cbm"
                                                    value="{{ old('total_cbm', 0) }}" name="total_cbm" />
                                                <input type="hidden" id="parcel_item_status"
                                                    name="parcel_item_status"
                                                    value="{{ old('parcel_item_status', App\Enums\ParcelItemStatus::PENDING) }}">
                                                <input type="hidden" id="shipping_fee_status"
                                                    name="shipping_fee_status"
                                                    value="{{ old('shipping_fee_status', App\Enums\ShippingFeeStatus::PENDING) }}">
                                                <input type="hidden" id="total_cbm_weight"
                                                    name="total_valumetric_weight" value="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="cbm-row"></div>
                                <div class="text-right">
                                    <a href="javascript:void(0)"
                                        class="text-white font-weight-bold text-right btn btn-success" id="add_item">
                                        <i class="fa fa-plus me-2"></i> {{ __('parcel.add_another_parcel') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="row">

                            <input type="hidden" id="merchant_id" name="merchant_id"
                                value="{{ Auth::user()->merchant->id ?? '' }}" />
                            <input type="hidden" id="merchantVat" name="vat_tex" value="0" />
                            <input type="hidden" id="merchantCodCharge" name="cod_charge" value="0" />
                            <input type="hidden" id="chargeDetails" name="chargeDetails" value="" />
                            <input type="hidden" name="distance_km" class="distance_km"
                                value="{{ old('distance_km') }}" />
                            <input id="special_discount" type="hidden" name="special_discount" class="form-control"
                                value="{{ old('special_discount', 0) }}" required="">
                            <input type="hidden" name="merchant_discount" class="merchant_discount"
                                value="{{ old('merchant_discount', @$merchant->discount) }}" />
                            <input type="hidden" name="merchant_discount_amount" class="merchant_discount_amount"
                                value="{{ old('merchant_discount_amount', 0) }}" />
                            <input type="hidden" id="discount_eligible" value="true" />
                            <input type="hidden" id="package_total_row" name="package_total_row" value="0">

                        </div>


                        <div class="text-center">
                            <button type="button" class="btn btn-sm btn-primary"
                                data-bs-target="#exampleModalToggle" data-bs-toggle="modal">Get Qoute</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel"
            tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalToggleLabel">Your Quotes</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-group">

                                    <li class="list-group-item profile-list-group-item">
                                        <span class="float-left font-weight-bold">Volumetric Weight</span>
                                        <span class="float-right"><span id="total_valumetric_weight">0
                                                kg</span></span>
                                    </li>
                                    <li class="list-group-item profile-list-group-item">
                                        <span class="float-left font-weight-bold">{{ __('parcel.distance') }}</span>
                                        <span class="float-right"><span id="distance_km">{{ __('0.00') }}</span>
                                            {{ __('parcel.km') }}</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">

                                <ul class="list-group">

                                    <li class="list-group-item profile-list-group-item d-none">
                                        <span
                                            class="float-left font-weight-bold">{{ __('parcel.Cash_Collection') }}</span>
                                        <span class="float-right"
                                            id="totalCashCollection">{{ __('0.00') }}</span>
                                    </li>
                                    <li class="list-group-item profile-list-group-item">
                                        <span
                                            class="float-left font-weight-bold">{{ __('parcel.Delivery_Charge') }}</span>
                                        <span class="float-right"
                                            id="deliveryChargeAmount">{{ __('0.00') }}</span>
                                    </li>
                                    <li class="list-group-item profile-list-group-item d-none">
                                        <span
                                            class="float-left font-weight-bold">{{ __('parcel.special_discount') }}</span>
                                        <span class="float-right"> - <span
                                                id="special_discount_amount">{{ __('0.00') }}</span></span>
                                    </li>
                                    <li class="list-group-item profile-list-group-item">
                                        <span class="float-left font-weight-bold">{{ __('parcel.sub_total') }}</span>
                                        <span class="float-right font-weight-bold"
                                            id="sub_total">{{ __('0.00') }}</span>
                                    </li>
                                    <li class="list-group-item profile-list-group-item d-none">
                                        <span
                                            class="float-left font-weight-bold">{{ __('reports.COD_Charge') }}</span>
                                        <span class="float-right" id="codChargeAmount">{{ __('0.00') }}</span>
                                    </li>
                                    <li class="list-group-item profile-list-group-item hideShowLiquidFragile">
                                        <span
                                            class="float-left font-weight-bold">{{ __('parcel.Liquid/Fragile_Charge') }}</span>
                                        <span class="float-right"
                                            id="liquidFragileAmount">{{ __('0.00') }}</span>
                                    </li>
                                    <li class="list-group-item profile-list-group-item rushHourService">
                                        <span
                                            class="float-left font-weight-bold">{{ __('parcel.rush_hour_service') }}</span>
                                        <span class="float-right"
                                            id="rushHourServiceAmount">{{ __('0.00') }}</span>
                                    </li>
                                    <li class="list-group-item profile-list-group-item scheduledService">
                                        <span
                                            class="float-left font-weight-bold">{{ __('parcel.scheduled_amount') }}</span>
                                        <span class="float-right"
                                            id="scheduledServiceAmount">{{ __('0.00') }}</span>
                                    </li>
                                    <li class="list-group-item profile-list-group-item extraCostCheckService">
                                        <span
                                            class="float-left font-weight-bold">{{ __('parcel.extra_cost') }}</span>
                                        <span class="float-right" id="extraCostAmount">{{ __('0.00') }}</span>
                                    </li>
                                    <li class="list-group-item profile-list-group-item" id="packagingShow">
                                        <span
                                            class="float-left font-weight-bold">{{ __('parcel.packaging_charge') }}</span>
                                        <span class="float-right" id="packagingAmount">{{ __('0.00') }}</span>
                                    </li>
                                    <li class="list-group-item profile-list-group-item">
                                        <span
                                            class="float-left font-weight-bold">{{ __('parcel.Total_Charge') }}</span>
                                        <span class="float-right"
                                            id="totalDeliveryChargeAmount">{{ __('0.00') }}</span>
                                    </li>
                                    <li class="list-group-item profile-list-group-item d-none">
                                        <span class="float-left font-weight-bold">{{ __('parcel.discount') }}
                                            (-)</span>
                                        <span class="float-right" id="merchant_discount">{{ __('0.00') }}</span>
                                    </li>
                                    <li class="list-group-item profile-list-group-item ">
                                        <span class="float-left font-weight-bold">{{ __('parcel.Vat') }}</span>
                                        <span class="float-right" id="VatAmount">{{ __('0.00') }}</span>
                                    </li>
                                    <li class="list-group-item profile-list-group-item">
                                        <span
                                            class="float-left font-weight-bold text-primary">{{ __('parcel.total_shipping_fee') }}</span>
                                        <span class="float-right text-primary"
                                            id="netPayable">{{ __('0.00') }}</span>
                                    </li>

                                </ul>
                                <div class="mt-2 text-center">
                                    <a class="btn btn-sm btn-primary" target="_blank"
                                        href="{{ route('merchant-panel.parcel.create', ['fresh' => 1]) }}">{{ __('levels.create_shipment') }}</a>
                                </div>
                            </div>


                        </div>


                    </div>
                </div>
            </div>
        </div>



    </div>


    <script src="{{ static_asset('backend') }}/vendor/jquery/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="{{ static_asset('backend') }}/vendor/bootstrap-five/bootstrap.min.js"></script>
    <script src="{{ static_asset('backend') }}/vendor/bootstrap/js/bootstrap.bundle.js"></script>
    <script src="{{ static_asset('backend') }}/libs/js/datepicker.min.js"></script>
    <script src="{{ static_asset('backend') }}/libs/js/custom.js"></script>
    <script src="{{ static_asset('backend') }}/js/lang.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ static_asset('backend/vendor') }}/toastr/toastr.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ static_asset('backend') }}/js/map/current_location.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $(".sidebar-dark").addClass('show');
            if (window.innerWidth <= 991) {
                $(".sidebar-dark").removeClass('show');
                $(".sidebar-dark").addClass('text-bg-dark');
            }
            $(window).resize(function() {
                if (window.innerWidth >= 991) {
                    $('.sidebar-offcanvas').addClass('show');
                    $('.sidebar-offcanvas').removeClass('text-bg-dark');
                } else {
                    $('.sidebar-offcanvas').removeClass('show');
                    $('.sidebar-offcanvas').addClass('text-bg-dark');
                }
            });
        });

        $(document).ready(function() {
            // Will wait for everything on the page to load.
            $(window).bind('load', function() {
                $('.overlay, body').addClass('loaded');
                setTimeout(function() {
                    $('.overlay').css({
                        'display': 'none'
                    })
                }, 2000)
            });
            // Will remove overlay after 1min for users cannnot load properly.
            setTimeout(function() {
                $('.overlay, body').addClass('loaded');
            }, 60000);
        })
    </script>
    <script>
        @if (Session::has('message'))
            var type = " {{ Session::get('alert-type', 'info') }}"
            switch (type) {
                case 'info':
                    toastr.info(" {{ Session::get('message') }} ");
                    break;
                case 'success':
                    toastr.success(" {{ Session::get('message') }} ");
                    break;
                case 'warning':
                    toastr.warning(" {{ Session::get('message') }} ");
                    break;
                case 'error':
                    toastr.error(" {{ Session::get('message') }} ");
                    break;
            }
        @endif
    </script>
    {!! Toastr::message() !!}
    @if (env('DEMO') && env('DEMO') !== '')
        <script type="text/javascript">
            "use strict";
            $(function() {
                $('input').attr('autocomplete', 'off');
            });
        </script>
    @endif
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var yes = "{{ __('delete.yes') }}";
        var cancel = "{{ __('delete.cancel') }}";
    </script>
    <script type="text/javascript">
        "use strict";
        $(function() {
            $('.demo-login-btn').click(function() {
                $('#email').attr('value', $(this).data('email'));
                $('#password').attr('value', $(this).data('password'));
            });
            $('input').attr('autocomplete', 'off');
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        var deliverChargeUnitParcelUrl = '{{ route('merchant-panel.getqoute.parcel.deliveryCharge.unitParcel.get') }}';
        var deliverChargeUrl = '{{ route('merchant-panel.getqoute.parcel.deliveryCharge.get') }}';
        var merchantData = {
            "vat": "0.00",
            "cod_charges": {
                "inside_city": "0",
                "sub_city": "0",
                "outside_city": "0"
            }
        };
        var merchantUrl = '{{ route('parcel.getquote.merchant.get') }}';
        var receiverSuggestion = '{{ route('getqoute.get.receiver.suggestions') }}';
        $(document).ready(function() {
            $(".select2").select2();
        });
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        @if (!empty($errors->all()))
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}")
            @endforeach
        @endif
    </script>
    <script>
        var fromcity = "{{ url('country/by/city') }}/";
        var fromdistric_url = "{{ url('city/by/district') }}/";
        var fromtown_url = "{{ url('district/by/town') }}/";
        var fromportalcode_url = "{{ url('town/by/portal_code') }}/";
        var fromGetCityUrl = "{{ url('town/by/city') }}/";
        var getFromHubInfoUrl = "{{ url('get/hub/info') }}/";

        var tocountries_url = "{{ url('get-qoute/deliverycharge/tocountries') }}";
        var tocities_url = "{{ url('get-qoute/deliverycharge/tocities') }}";
        var todistrict_url = "{{ url('get-qoute/deliverycharge/todistrict') }}";
        var totownurl = "{{ url('get-qoute/deliverycharge/totown') }}";
        var toportalcodeurl = "{{ url('get-qoute/deliverycharge/toportalcode') }}";

        var get_shipping_type_url = "{{ route('get-qoute.get.shipping.type') }}";
        var add_item = "{{ route('get-qoute.parcel.add.item') }}";
        var location_from_hubs = "{{ route('get-qoute.parcel.location.from.hubs') }}";
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
    <script src="{{ static_asset('backend/js/merchant_panel/parcel/get_qoute.js') }}"></script>

</body>

</html>
