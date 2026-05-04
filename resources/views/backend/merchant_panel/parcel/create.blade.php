@extends('backend.partials.master')
@section('title')
    {{ __('parcel.title') }} {{ __('levels.add') }}
@endsection
@section('maincontent')
    <div class="container-fluid dashboard-content">
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
                                    <a href="{{ route('merchant-panel.parcel.index') }}" class="breadcrumb-link">
                                        {{ __('parcel.title') }}</a>
                                    </li>
                                <li class="breadcrumb-item">
                                    <a href="" class="breadcrumb-link active">
                                        {{ __('levels.create') }}
                                    </a>
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
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
                    <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                        <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('parcel.create_parcel') }}</h4>
                    </div>
                    <div class="card-body">
                        <!-- <h2 class="pageheader-title">{{ __('parcel.create_parcel') }}</h2> -->
                        <form action="{{ route('merchant-panel.parcel.store') }}" method="POST" id="basicform" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('parcel.sending_from') }}</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for="from_state_id">{{ __('parcel.province_state') }} <span class="text-danger">*</span></label>
                                                        <select name="from_state_id" id="from_state_id" data-url="{{ route('get.province.city') }}" class="form-control select2" style="width: 100%">
                                                            <option value="">{{ __('menus.select') }} {{ __('parcel.province_state') }}</option>
                                                            @foreach ($provinces as $province)
                                                                <option value="{{ $province->id }}" @selected(old('from_state_id') == $province->id)>{{ $province->name }}({{ $province->province_code }})</option>
                                                            @endforeach
                                                        </select>
                                                        @error('from_state_id')
                                                            <span class="text-danger mt-2">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <input type="hidden" id="from_point" value="1" name="from_point_type">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="from_city_id">{{ __('parcel.city') }} <span class="text-danger">*</span></label>
                                                        <select name="from_city_id" id="from_city_id" class="form-control select2" data-url="{{ route('city.wise.portal_code') }}" data-old-value="{{ old('from_city_id') }}" style="width: 100%">
                                                            <option value="">{{ __('menus.select') }} {{ __('parcel.city') }}</option>
                                                            @foreach ($cities as $city)
                                                                @if (old('from_state_id') == $city->province_id)
                                                                    <option value="{{ $city->id }}" @selected(old('from_city_id') == $city->id)>{{ $city->name }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                        @error('from_city_id')
                                                            <span class="text-danger mt-2">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="from_portal_code">{{ __('parcel.portal_code') }} <span class="text-danger">*</span></label>
                                                        <input type="text" name="from_portal_code" id="from_portal_code" placeholder="{{ __('parcel.portal_code') }}"class="form-control" value="{{ old('from_portal_code') }}">
                                                        @error('from_portal_code')
                                                            <span class="text-danger mt-2">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="mb-0" style="font-size: 18px; font-weight: 500;">Sender</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    @php
                                                        $inside       = $merchant->cod_charges['inside_city'];
                                                        $sub_city     = $merchant->cod_charges['sub_city'];
                                                        $outside_city = $merchant->cod_charges['outside_city'];
                                                    @endphp
                                                    <input type="hidden" id="merchanturl" data-url="{{ route('get.merchant.cod') }}" />
                                                    <input type="hidden" id="inside_city" value="{{ @$inside?? 0 }}" />
                                                    <input type="hidden" id="sub_city" value="{{ @$sub_city?? 0 }}" />
                                                    <input type="hidden" id="outside_city" value="{{ @$outside_city?? 0 }}" />
                                                    <input type="hidden" id="from_account_type" name="from_account_type" value="1" />
                                                </div>
                                                <div class="col-md-12" id="company_name_field" style="display: none;"></div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="first_name">{{ __('levels.first_name') }}</label>
                                                        <input id="first_name" type="text" name="first_name" placeholder="{{ __('placeholder.Enter_first_name') }}" class="form-control" value="{{ old('first_name') }}">
                                                        @error('first_name')
                                                            <small class="text-danger mt-2">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="last_name">{{ __('levels.last_name') }}</label>
                                                        <input id="last_name" type="text" name="last_name" placeholder="{{ __('placeholder.Enter_last_name') }}" class="form-control" value="{{ old('last_name') }}">
                                                        @error('last_name')
                                                            <small class="text-danger mt-2">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="pickup_phone">{{ __('parcel.phone_number') }}</label>
                                                        <input id="pickup_phone" type="text" name="pickup_phone" placeholder="{{ __('levels.pickup') }} {{ __('levels.phone') }}" class="form-control" value="{{ old('pickup_phone') }}">
                                                        @error('pickup_phone')
                                                            <small class="text-danger mt-2">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="sender_email">{{ __('levels.email') }}</label>
                                                        <input placeholder="{{ __('levels.enter_email') }}" type="text" id="sender_email" name="sender_email" class="form-control" />
                                                        @error('sender_email')
                                                            <small class="text-danger mt-2">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="pickup_address">Address<span class="text-danger">*</span></label>
                                                        <input type="text" name="pickup_address" id="pickup_address" placeholder="Enter address"class="form-control" value="{{ old('pickup_address') }}">
                                                        @error('pickup_address')
                                                            <span class="text-danger mt-2">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('parcel.sending_to') }}</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for="to_state_id">{{ __('parcel.province_state') }} <span class="text-danger">*</span></label>
                                                        <select style="width: 100%" id="to_state_id" class="form-control select2" data-url="{{ route('get.province.city') }}" name="to_state_id">
                                                            <option value="">{{ __('menus.select') }} {{ __('parcel.province_state') }}</option>
                                                            @foreach ($provinces as $province)
                                                                <option value="{{ $province->id }}" @selected(old('to_state_id') == $province->id)>{{ $province->name }}({{ $province->province_code }})</option>
                                                            @endforeach
                                                        </select>
                                                        @error('to_state_id')
                                                            <span class="text-danger mt-2">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <input type="hidden" id="to_point" value="1" name="to_point_type">
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for="to_city_id">{{ __('parcel.city') }} <span class="text-danger">*</span></label>
                                                        <select style="width: 100%" id="to_city_id" class="form-control select2" data-url="{{ route('city.wise.portal_code') }}" data-old-value="{{ old('to_city_id') }}" name="to_city_id">
                                                            <option value="">{{ __('menus.select') }} {{ __('parcel.city') }}</option>
                                                            @foreach ($cities as $city)
                                                                @if (old('to_state_id') == $city->province_id)
                                                                    <option value="{{ $city->id }}" @selected(old('to_city_id') == $city->id)>{{ $city->name }}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                        @error('to_city_id')
                                                            <span class="text-danger mt-2">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for="to_portal_code">{{ __('parcel.portal_code') }} <span class="text-danger">*</span></label>
                                                        <input type="text" name="to_portal_code" id="to_portal_code" placeholder="{{ __('parcel.portal_code') }}" class="form-control" value="{{ old('to_portal_code') }}">
                                                        @error('to_portal_code')
                                                            <span class="text-danger mt-2">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('parcel.recipient') }}</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <input type="hidden" id="to_account_type" name="to_account_type" value="1" />
                                                <div class="col-md-12 to_company_name_field" style="display: none"></div>
    
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for="customer_first_name">{{ __('levels.first_name') }} <span class="text-danger">*</span></label>
                                                        <input id="customer_first_name" type="text" name="customer_first_name" placeholder="{{ __('placeholder.Enter_first_name') }}" class="form-control" value="{{ old('customer_first_name') }}">
                                                        @error('customer_first_name')
                                                            <small class="text-danger mt-2">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for="customer_last_name">{{ __('levels.last_name') }} <span class="text-danger">*</span></label>
                                                        <input id="customer_last_name" type="text" name="customer_last_name" placeholder="{{ __('placeholder.Enter_last_name') }}" class="form-control" value="{{ old('customer_last_name') }}">
                                                        @error('customer_last_name')
                                                            <small class="text-danger mt-2">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for="customer_phone">{{ __('parcel.phone_number') }}</label>
                                                        <span class="text-danger">*</span>
                                                        <input id="customer_phone" type="text" name="customer_phone" placeholder="{{ __('placeholder.enter_phone_number') }}" class="form-control" value="{{ old('customer_phone') }}">
                                                        @error('customer_phone')
                                                            <small class="text-danger mt-2">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-12">
                                                    <div class="form-group">
                                                        <label for="receiver_email">{{ __('levels.email') }}</label>
                                                        <input id="receiver_email" type="text" name="receiver_email" placeholder="{{ __('parcel.receiver_email') }}" class="form-control" value="{{ old('receiver_email') }}">
                                                        @error('receiver_email')
                                                            <small class="text-danger mt-2">{{ $message }}</small>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="customer_address">Address<span class="text-danger">*</span></label>
                                                        <input type="text" name="customer_address" id="customer_address" placeholder="Enter address"class="form-control" value="{{ old('customer_address') }}">
                                                        @error('customer_address')
                                                            <span class="text-danger mt-2">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="pickup_location" >
                                            <span class="from_location_heading">Pickup Point</span>
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input type="hidden" id="pickup_lat" name="pickup_lat" required="" value="{{ old('pickup_lat') }}">
                                        <input type="hidden" id="pickup_long" name="pickup_long" required="" value="{{ old('pickup_long') }}">
                                        <div class="main-search-input-item location location-search">
                                            <div id="autocomplete-container" class="form-group random-search">
                                                <input id="autocomplete-inputs" type="text" name="pickup_location" class="recipe-search2 form-control autocomplete-inputs" value="{{ old('pickup_location') }}" placeholder="Enter pickup point">
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
                                        <input type="hidden" id="drop_latitude" name="drop_latitude" required="" value="{{ old('drop_latitude') }}">
                                        <input type="hidden" id="drop_longitude" name="drop_longitude" required="" value="{{ old('drop_longitude') }}">
                                        <div class="main-search-input-item location location-search">
                                            <input id="autocomplete" type="text" name="drop_location" class="recipe-search2 form-control" value="{{ old('drop_location') }}" required="" placeholder="Enter Delivery point">
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
                                                        <option value="1" @selected(old('package_type_id') == 1)>{{ __('parcel.courier_document') }}</option>
                                                        <option value="2" @selected(old('package_type_id') == 2)>{{ __('parcel.parcel_type') }}</option>
                                                        <option value="2">{{ __('Food') }}</option>
                                                    </select>
                                                    @error('package_type_id')
                                                        <span class="text-danger">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class=" col-md-6  col-lg-3 col-xl-4 col-sm-12 mb-2">
                                                <label for="quantity">{{ __('parcel.quantity') }} <span class="text-danger">*</span></label>
                                                <div class="form-group">
                                                    <input id="quantity" type="number" name="quantity" placeholder="{{ __('parcel.quantity') }}" class="form-control" value="{{ old('quantity', 1) }}">
                                                </div>
                                                @error('quantity')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12 mb-2 parcelTypeId d-none">
                                                <label for="local_weight">{{ __('parcel.weight') }} <span class="text-danger">*</span></label>
                                                <div class="form-group">
                                                    <input id="local_weight" type="number" step="any" name="local_weight" placeholder="{{ __('parcel.weight') }}" class="form-control" value="{{ old('local_weight') }}">
                                                </div>
                                                @error('local_weight')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12">
                                                <div class="form-group mt-4">
                                                    <div class="preview-block">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input extra_cost" id="extra_cost"
                                                                name="extra_cost" onclick="extraCostMainCheck(this);">
                                                            <label class="custom-control-label" for="extra_cost">{{ __('parcel.extra_cost') }}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12 extraCostCheck1 {{ old('extra_cost') ? 'd-block' : 'd-none' }}">
                                                <div class="form-group">
                                                    <label for="extra_cost_amount">{{ __('parcel.extra_cost_amount') }}<span class="text-danger">*</span></label>
                                                    <input type="number" name="extra_cost_amount" id="extra_cost_amount" class="form-control extra_cost_amount" placeholder="{{ __('parcel.extra_cost_amount') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12 extraCostCheck1 {{ old('extra_cost') ? 'd-block' : 'd-none' }}">
                                                <div class="form-group">
                                                    <label for="extra_cost_description">{{ __('parcel.extra_cost_description') }}</label>
                                                    <input type="text" name="extra_cost_description" id="extra_cost_description" class="form-control extra_cost_description" placeholder="{{ __('parcel.extra_cost_description') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12 mb-2">
                                                <div class="form-group">
                                                    <label for="content_parcel">{{ __('parcel.content_parcel') }}</label>
                                                    <input id="content_parcel" type="text" name="content_parcel" placeholder="{{ __('parcel.content_parcel') }}" class="form-control" value="{{ old('content_parcel') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12 mb-2">
                                                <div class="form-group">
                                                    <label for="parcel_value_amount">Parcel Value Amount</label>
                                                    <input id="parcel_value_amount" type="number" name="parcel_value" class="form-control" placeholder="0.00" value="{{ old('parcel_value') }}" step="0.01">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12 mb-2">
                                                <div class="form-group">
                                                    <label for="parcel_file">Attach File/Document</label>
                                                    <input id="parcel_file" type="file" name="parcel_file" class="form-control" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                                    <small class="text-muted d-block mt-1">Maximum file size: 2 MB.</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <!-- <div class="col-md-12 text-end">
                                                <button type="button" class="cloneMainParcelItem text-white btn btn-info mt-2">
                                                    <i class="fa fa-clone me-2"></i> {{ __('parcel.copy_this_parcel') }}
                                                </button>
                                            </div> -->
                                            <div class="col-md-12 col-sm-12 cbm-input-group mb-2">
                                                <div class="d-flex align-items-end justify-content-center h-100">
                                                    <input type="hidden" id="main_unit_parcel_service_cost" name="main_unit_parcel_service_cost" value="0">
                                                    <input type="hidden" id="main_total_cbm" name="main_total_cbm" value="0" />
                                                    <input type="hidden" id="main_total_weight" name="main_total_weight" value="0" />
                                                    <input type="hidden" id="total_weight" name="total_weight" value="0" />
                                                    <input type="hidden" id="total_cbm" value="{{ old('total_cbm', 0) }}" name="total_cbm" />
                                                    <input type="hidden" id="parcel_item_status" name="parcel_item_status" value="{{ old('parcel_item_status', App\Enums\ParcelItemStatus::PENDING) }}">
                                                    <input type="hidden" id="shipping_fee_status" name="shipping_fee_status" value="{{ old('shipping_fee_status', App\Enums\ShippingFeeStatus::PENDING) }}">
                                                    <input type="hidden" id="total_cbm_weight" name="total_valumetric_weight" value="0">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cbm-row"></div>
                                    <div class="text-right">
                                        <a href="javascript:void(0)" class="text-white font-weight-bold text-right btn btn-success" id="add_item">
                                            <i class="fa fa-plus me-2"></i> {{ __('parcel.add_another_parcel') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="cbm-box cbm-main mb-3 pt-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="who_pays_either">{{ __('parcel.who_pays_either') }} <span class="text-danger">*</span></label>
                                            <select name="who_pays_either" class="form-control" id="who_pays_either" style="width: 100% !important" required>
                                                <option value="">{{ __('levels.select') }} {{ __('parcel.who_pays_either') }}</option>
                                                <option value="{{ App\Enums\WhoPays::RECIPIENT }}" @selected(old('who_pays_either') == App\Enums\WhoPays::RECIPIENT)>{{ __('parcel.recipient') }}</option>
                                                <option value="{{ App\Enums\WhoPays::SENDER }}" @selected(old('who_pays_either') == App\Enums\WhoPays::SENDER)>{{ __('parcel.sender') }}</option>
                                                <option value="{{ App\Enums\WhoPays::THIRD_PARTY }}" @selected(old('who_pays_either') == App\Enums\WhoPays::THIRD_PARTY)>Third Person</option>
                                            </select>
                                            @error('who_pays_either')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                @php
                                    $paymentIntent = old('payment_intent', 'pay_now');
                                    $mpesaPromptSent = session('mpesa_prompt_sent', false);
                                @endphp
                                <div class="row d-none" id="senderPaymentSection">
                                    <div class="col-md-6">
                                        <div class="col-sm-12 mb-2">
                                            <div class="form-group">
                                                <label>Sender Payment</label> <span class="text-danger">*</span>
                                                <div class="d-flex align-items-center gap-3">
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input" id="payment_now" name="payment_intent" value="pay_now" @checked($paymentIntent === 'pay_now')>
                                                        <label class="custom-control-label" for="payment_now">Pay now (M-Pesa)</label>
                                                    </div>
                                                    <div class="custom-control custom-radio ml-3" id="paymentLaterOption">
                                                        <input type="radio" class="custom-control-input" id="payment_later" name="payment_intent" value="pay_later" @checked($paymentIntent === 'pay_later')>
                                                        <label class="custom-control-label" for="payment_later">Pay later</label>
                                                    </div>
                                                </div>
                                                <small class="text-muted d-block mt-1">Pay now sends an M-Pesa prompt. Pay later allows parcel creation.</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 {{ $paymentIntent === 'pay_later' ? 'd-none' : '' }}" id="mpesaPhoneGroup">
                                        <div class="form-group">
                                            <label for="mpesa_sender_phone">Sender M-Pesa Number</label> <span class="text-danger">*</span>
                                            <input id="mpesa_sender_phone" name="mpesa_sender_phone" type="text" class="form-control" placeholder="07XXXXXXXX or 2547XXXXXXXX" value="{{ old('mpesa_sender_phone', Auth::user()->mobile) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6 d-flex align-items-end mb-2 {{ $paymentIntent === 'pay_later' || $mpesaPromptSent ? 'd-none' : '' }}" id="mpesaPayBtnGroup">
                                        <button type="button" class="btn btn-success" id="mpesaPayBtn">Pay with M-Pesa</button>
                                    </div>
                                </div>
                                <div class="row d-none" id="receiverPaymentSection">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="receiver_mpesa_phone">Receiver M-Pesa Number</label> <span class="text-danger">*</span>
                                            <input id="receiver_mpesa_phone" type="text" name="receiver_mpesa_phone" class="form-control" placeholder="07XXXXXXXX or 2547XXXXXXXX" value="{{ old('receiver_mpesa_phone') }}">
                                            <small class="text-muted d-block mt-1">Used for M-Pesa prompt to receiver.</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-md-12">
                                        <div class="form-group">
                                            <label for="note">{{ __('parcel.note') }}</label>
                                            <textarea id="note" name="note" class="form-control" rows="3">{{ old('note') }}</textarea>
                                        </div>
                                    </div>
                                    <input type="hidden" id="merchant_id" name="merchant_id" value="{{ Auth::user()->merchant->id }}" />
                                    <input type="hidden" id="merchantVat" name="vat_tex" value="0" />
                                    <input type="hidden" id="merchantCodCharge" name="cod_charge" value="0" />
                                    <input type="hidden" id="chargeDetails" name="chargeDetails" value="" />
                                    <input type="hidden" name="distance_km" class="distance_km" value="{{ old('distance_km') }}" />
                                    <input id="special_discount" type="hidden" name="special_discount" class="form-control" value="{{ old('special_discount',0) }}" required="">
                                    <input type="hidden" name="merchant_discount" class="merchant_discount" value="{{ old('merchant_discount', $merchant->discount) }}" />
                                    <input type="hidden" name="merchant_discount_amount" class="merchant_discount_amount" value="{{ old('merchant_discount_amount', 0) }}" />
                                    <input type="hidden" id="discount_eligible" value="true" />
                                    <input type="hidden" id="package_total_row" name="package_total_row" value="0">
                                    <input type="hidden" id="mpesa_checkout_request_id" name="mpesa_checkout_request_id" value="{{ old('mpesa_checkout_request_id') }}">
                                    
                                </div>
                                <!-- <div class="row pt-1">
                                    <div class="form-group">
                                        <div class="preview-block">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="policy" name="policy" @if (old('policy')) checked @endif>
                                                <label class="custom-control-label" for="policy"><span>I agree to <a class="text-primary">{{ settings()->name }}</a> Privacy Policy &amp; Terms.</span></label>
                                            </div>
                                        </div>
                                    </div>
                                    @error('policy')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div> -->
                                <div class="row">
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12  d-flex justify-content-end">
                                        <a href="{{ route('merchant-panel.parcel.index') }}" class="btn btn-space btn-secondary">{{ __('levels.cancel') }}</a>
                                        <button type="submit" id="parcelSaveBtn" class="btn btn-space btn-primary {{ in_array((int) old('who_pays_either'), [\App\Enums\WhoPays::SENDER, \App\Enums\WhoPays::THIRD_PARTY], true) && $paymentIntent === 'pay_now' && !$mpesaPromptSent ? 'd-none' : '' }}">{{ __('levels.save') }}</button>
                                    </div>
                                </div>

                            </div>
                        </form>
                        <form id="mpesaPayForm" class="d-none" action="{{ route('merchant-panel.mpesa.pay') }}" method="POST">
                            @csrf
                            <input type="hidden" name="amount" id="mpesa_amount" value="">
                            <input type="hidden" name="phone" id="mpesa_phone" value="">
                            <input type="hidden" name="parcel_payload" id="mpesa_parcel_payload" value="">
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
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
                                <span class="float-right"><span id="total_valumetric_weight">0 kg</span></span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('parcel.distance') }}</span>
                                <span class="float-right"><span id="distance_km">{{ __('0.00') }}</span> {{ __('parcel.km') }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item d-none">
                                <span class="float-left font-weight-bold">{{ __('parcel.Cash_Collection') }}</span>
                                <span class="float-right" id="totalCashCollection">{{ __('0.00') }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('parcel.Delivery_Charge') }}</span>
                                <span class="float-right" id="deliveryChargeAmount">{{ __('0.00') }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('parcel.special_discount') }}</span>
                                <span class="float-right"> - <span id="special_discount_amount">{{ __('0.00') }}</span></span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('parcel.sub_total') }}</span>
                                <span class="float-right font-weight-bold" id="sub_total">{{ __('0.00') }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item d-none">
                                <span class="float-left font-weight-bold">{{ __('reports.COD_Charge') }}</span>
                                <span class="float-right" id="codChargeAmount">{{ __('0.00') }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item hideShowLiquidFragile">
                                <span class="float-left font-weight-bold">{{ __('parcel.Liquid/Fragile_Charge') }}</span>
                                <span class="float-right" id="liquidFragileAmount">{{ __('0.00') }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item extraCostCheckService">
                                <span class="float-left font-weight-bold">{{ __('parcel.extra_cost') }}</span>
                                <span class="float-right" id="extraCostAmount">{{ __('0.00') }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('parcel.Total_Charge') }}</span>
                                <span class="float-right" id="totalDeliveryChargeAmount">{{ __('0.00') }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('parcel.discount') }} (-)</span>
                                <span class="float-right" id="merchant_discount">{{ __('0.00') }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('parcel.Vat') }}</span>
                                <span class="float-right" id="VatAmount">{{ __('0.00') }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('parcel.total_shipping_fee') }}</span>
                                <span class="float-right" id="netPayable">{{ __('0.00') }}</span>
                            </li>
                            <li class="list-group-item profile-list-group-item">
                                <span class="float-left font-weight-bold">{{ __('parcel.Current_payable') }}</span>
                                <span class="float-right" id="currentPayable">{{ __('0.00') }}</span>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css"  />
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        var deliverChargeUnitParcelUrl = '{{ route('merchant-panel.parcel.deliveryCharge.unitParcel.get') }}';
        var deliverChargeUrl = '{{ route('merchant-panel.parcel.deliveryCharge.get') }}';
        var merchantData = @json($merchant);
        var merchantUrl = '{{ route('parcel.merchant.get') }}';
        var receiverSuggestion = '{{ route('get.receiver.suggestions') }}';
        var parcelDraftStorageKey = 'merchant_parcel_create_draft_v1';
        var forceFreshCreate = {{ request()->boolean('fresh') ? 'true' : 'false' }};
        var hasServerOldInput = {{ count(session()->getOldInput() ?? []) > 0 ? 'true' : 'false' }};
        var draftSaveTimer = null;
        $(document).ready(function() {
            $(".select2").select2();
        });
        if (window.bootstrap && bootstrap.Tooltip) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
        }

        @if (!empty($errors->all()))
            @foreach ($errors->all() as $error)
                toastr.error("{{$error}}")
            @endforeach
        @endif

        var senderValue = '{{ App\Enums\WhoPays::SENDER }}';
        var receiverValue = '{{ App\Enums\WhoPays::RECIPIENT }}';
        var thirdPartyValue = '{{ App\Enums\WhoPays::THIRD_PARTY }}';
        var mpesaPromptSent = {{ session('mpesa_prompt_sent') ? 'true' : 'false' }};
        function toggleSenderPaymentSection() {
            var whoPays = $('#who_pays_either').val();
            var isSenderLike = (String(whoPays) === String(senderValue)) || (String(whoPays) === String(thirdPartyValue));
            if (isSenderLike) {
                $('#senderPaymentSection').removeClass('d-none');
                $('#receiverPaymentSection').addClass('d-none');
                var isThirdParty = String(whoPays) === String(thirdPartyValue);
                if (isThirdParty) {
                    $('#paymentLaterOption').addClass('d-none');
                    $('#payment_later').prop('checked', false).prop('disabled', true);
                    $('#payment_now').prop('checked', true);
                } else {
                    $('#paymentLaterOption').removeClass('d-none');
                    $('#payment_later').prop('disabled', false);
                }
                var intent = $('input[name="payment_intent"]:checked').val();
                if (intent === 'pay_later') {
                    mpesaPromptSent = false;
                    $('#parcelSaveBtn').removeClass('d-none');
                    $('#mpesaPhoneGroup, #mpesaPayBtnGroup').addClass('d-none').css('display', 'none');
                    $('#mpesaPayBtn').addClass('d-none').css('display', 'none');
                } else {
                    $('#parcelSaveBtn').toggleClass('d-none', !mpesaPromptSent);
                    $('#mpesaPhoneGroup, #mpesaPayBtnGroup').removeClass('d-none').css('display', '');
                    $('#mpesaPayBtn').toggleClass('d-none', mpesaPromptSent).css('display', mpesaPromptSent ? 'none' : '');
                    if (mpesaPromptSent) {
                        $('#mpesaPayBtnGroup').addClass('d-none').css('display', 'none');
                    }
                }
            } else if (whoPays == receiverValue) {
                mpesaPromptSent = false;
                $('#senderPaymentSection').addClass('d-none');
                $('#receiverPaymentSection').removeClass('d-none');
                $('#parcelSaveBtn').removeClass('d-none');
                $('#mpesaPhoneGroup, #mpesaPayBtnGroup').addClass('d-none').css('display', 'none');
                $('#mpesaPayBtn').addClass('d-none').css('display', 'none');
            } else {
                mpesaPromptSent = false;
                $('#senderPaymentSection').addClass('d-none');
                $('#receiverPaymentSection').addClass('d-none');
                $('#parcelSaveBtn').removeClass('d-none');
                $('#mpesaPhoneGroup, #mpesaPayBtnGroup').addClass('d-none').css('display', 'none');
                $('#mpesaPayBtn').addClass('d-none').css('display', 'none');
            }
        }
        $(document).on('change', '#who_pays_either', toggleSenderPaymentSection);
        $(document).on('change', 'input[name="payment_intent"]', toggleSenderPaymentSection);
        $(document).on('click', 'input[name="payment_intent"]', toggleSenderPaymentSection);
        $(document).ready(function() {
            toggleSenderPaymentSection();
            $('#receiver_mpesa_phone').val($('#receiver_mpesa_phone').val() || $('#customer_phone').val());
        });

        $(document).on('change', '#parcel_file', function () {
            var file = this.files && this.files[0] ? this.files[0] : null;
            if (!file) {
                return;
            }

            var maxSizeBytes = 2 * 1024 * 1024;
            if (file.size > maxSizeBytes) {
                toastr.error('Attached file must be 2 MB or smaller.');
                $(this).val('');
            }
        });

        $('#basicform').on('submit', function (e) {
            var fileInput = document.getElementById('parcel_file');
            var file = fileInput && fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;
            if (file && file.size > (2 * 1024 * 1024)) {
                e.preventDefault();
                toastr.error('Attached file must be 2 MB or smaller.');
                return;
            }

            // Successful submit path: clear local draft so next create page is blank.
            try {
                sessionStorage.removeItem(parcelDraftStorageKey);
            } catch (err) {}
        });

        $(document).on('input', '#customer_phone', function () {
            if (!$('#receiver_mpesa_phone').val()) {
                $('#receiver_mpesa_phone').val($(this).val());
            }
        });

        $('#mpesaPayBtn').on('click', function () {
            var amountText = $('#currentPayable').text() || $('#netPayable').text() || $('#totalDeliveryChargeAmount').text();
            var amount = parseFloat(String(amountText).replace(/[^0-9.]/g, ''));
            if (!amount || amount <= 0) {
                toastr.error('Total charge is required before payment.');
                return;
            }
            var phoneInput = $('#mpesa_sender_phone').val();
            if (!phoneInput) {
                toastr.error('Sender M-Pesa number is required.');
                return;
            }
            $('#mpesa_amount').val(Math.ceil(amount));
            $('#mpesa_phone').val(phoneInput);
            var formArray = $('#basicform').serializeArray();
            var payload = {};
            formArray.forEach(function (item) {
                payload[item.name] = item.value;
            });
            $('#mpesa_parcel_payload').val(JSON.stringify(payload));
            saveParcelDraft();
            $('#mpesaPayForm').submit();
        });

        function draftNameSelector(name) {
            return '[name="' + String(name).replace(/"/g, '\\"') + '"]';
        }

        function saveParcelDraft() {
            try {
                var draft = {
                    created_at: Date.now(),
                    form_query: $('#basicform').serialize(),
                    mpesa_sender_phone: $('#mpesa_sender_phone').val() || '',
                };
                sessionStorage.setItem(parcelDraftStorageKey, JSON.stringify(draft));
            } catch (e) {
                // Ignore storage errors in private mode or restricted browsers.
            }
        }

        function restoreParcelDraft() {
            var raw;
            try {
                raw = sessionStorage.getItem(parcelDraftStorageKey);
            } catch (e) {
                return;
            }
            if (!raw) {
                return;
            }

            var draft;
            try {
                draft = JSON.parse(raw);
            } catch (e) {
                sessionStorage.removeItem(parcelDraftStorageKey);
                return;
            }
            if (!draft || !draft.form_query) {
                sessionStorage.removeItem(parcelDraftStorageKey);
                return;
            }

            // Expire old drafts after 2 hours.
            var maxAgeMs = 2 * 60 * 60 * 1000;
            if (draft.created_at && (Date.now() - draft.created_at) > maxAgeMs) {
                sessionStorage.removeItem(parcelDraftStorageKey);
                return;
            }

            var grouped = {};
            new URLSearchParams(draft.form_query).forEach(function(value, key) {
                if (!grouped[key]) {
                    grouped[key] = [];
                }
                grouped[key].push(value);
            });

            Object.keys(grouped).forEach(function(name) {
                if (name === '_token') {
                    return;
                }
                var values = grouped[name];
                var $fields = $(draftNameSelector(name));
                if (!$fields.length) {
                    return;
                }

                var type = ($fields.first().attr('type') || '').toLowerCase();
                if (type === 'checkbox' || type === 'radio') {
                    $fields.prop('checked', false);
                    values.forEach(function(val) {
                        $fields.filter('[value="' + String(val).replace(/"/g, '\\"') + '"]').prop('checked', true);
                    });
                } else if ($fields.first().is('select[multiple]')) {
                    $fields.val(values);
                } else {
                    $fields.val(values[values.length - 1]);
                }

                if ($fields.first().is('select')) {
                    $fields.trigger('change');
                }
            });

            if (draft.mpesa_sender_phone) {
                $('#mpesa_sender_phone').val(draft.mpesa_sender_phone);
            }

            toggleSenderPaymentSection();
        }

        $(document).on('input change', '#basicform input, #basicform textarea, #basicform select', function () {
            clearTimeout(draftSaveTimer);
            draftSaveTimer = setTimeout(saveParcelDraft, 250);
        });

        $(document).on('click', 'a[href="{{ route('merchant-panel.parcel.index') }}"]', function () {
            try {
                sessionStorage.removeItem(parcelDraftStorageKey);
            } catch (e) {}
        });

        $(document).ready(function () {
            if (forceFreshCreate) {
                try {
                    sessionStorage.removeItem(parcelDraftStorageKey);
                } catch (e) {}
            } else if (hasServerOldInput) {
                // On validation/M-Pesa redirect, trust Laravel old() values and avoid draft override.
                toggleSenderPaymentSection();
            } else {
                restoreParcelDraft();
            }
            clearTimeout(draftSaveTimer);
            draftSaveTimer = setTimeout(saveParcelDraft, 300);
        });
    </script>
    @include('backend.parcel.location.location_url')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js" ></script>
    <script src="{{ static_asset('backend/js/merchant_panel/parcel/create.js') }}"></script>
@endpush
