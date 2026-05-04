@extends('backend.partials.master')
@section('title')
    {{ __('delivery_charge.title') }} {{ __('levels.edit') }}
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
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                        class="breadcrumb-link">{{ __('levels.dashboard') }}</a></li>
                                <li class="breadcrumb-item"><a href="#"
                                        class="breadcrumb-link">{{ __('menus.settings') }}</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('delivery-charge.index') }}"
                                        class="breadcrumb-link">{{ __('delivery_charge.title') }}</a></li>
                                <li class="breadcrumb-item"><a href=""
                                        class="breadcrumb-link active">{{ __('levels.edit') }}</a></li>
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
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                        <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('delivery_charge.edit_delivery_charge') }}</h4>
                    </div>
                    <div class="card-body">
                        <!-- <h2 class="pageheader-title">{{ __('delivery_charge.edit_delivery_charge') }}</h2> -->

                        <form action="{{ route('delivery-charge.update', ['id' => $delivery_charge->id]) }}" method="POST"
                            enctype="multipart/form-data" id="basicform">
                            @method('PUT')
                            @csrf

                            <div class="row">

                                <div class="form-group col-12 col-md-6">
                                    <label for="delivery_type_id">{{ __('parcel.delivery_type') }}</label> <span
                                        class="text-danger">*</span>
                                    <select style="width: 100%" class="form-control select2" id="delivery_type_id"
                                        name="delivery_type_id">
                                        <option value="" selected> {{ __('menus.select') }}
                                            {{ __('menus.delivery_type') }}</option>
                                        @foreach ($deliveryTypes as $key => $status)
                                            <option
                                                @if ($status->key == 'same_day') value="1" @selected(old('delivery_type_id',$delivery_charge->delivery_type_id) == 1) @elseif($status->key == 'next_day') value="2" @selected(old('delivery_type_id',$delivery_charge->delivery_type_id) == 2) @elseif($status->key == 'sub_city') value="3" @selected(old('delivery_type_id',$delivery_charge->delivery_type_id) == 3) @elseif($status->key == 'outside_City') value="4" @selected(old('delivery_type_id',$delivery_charge->delivery_type_id) == 4) @endif>
                                                {{ __('deliveryType.' . $status->key) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('delivery_type_id')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="shipping_type">{{ __('parcel.shipping_type') }}</label> <span
                                            class="text-danger">*</span>
                                        <select id="shipping_type" name="shipping_type"
                                            class="form-control select2 @error('shipping_type') is-invalid @enderror">
                                            <option selected disabled>{{ __('menus.select') }}</option>
                                            @foreach (__('ShippingType') as $key => $shippingtype)
                                                <option {{ old('shipping_type') == $key ? 'selected' : '' }}
                                                    value="{{ $key }}" @selected(old('shipping_type', $key) == $delivery_charge->shipping_type)>
                                                    {{ $shippingtype }}</option>
                                            @endforeach
                                        </select>
                                        @error('shipping_type')
                                            <small class="text-danger mt-2">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="form-group  col-lg-12">
                                            <label for="from_country_id">{{ __('parcel.from_country_id') }}</label> <span
                                                class="text-danger">*</span>
                                            <select id="from_country_id" name="from_country_id"
                                                class="form-control select2">
                                                <option>{{ __('levels.select') }} {{ __('parcel.from_country_id') }}
                                                </option>
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country->id }}" @selected(old('from_country_id', $delivery_charge->from_country_id) == $country->id)>
                                                        {{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('from_country_id')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group  col-lg-12">
                                            <label for="from_city_id">{{ __('parcel.from_city_id') }}</label> <span
                                                class="text-danger">*</span>

                                            <select id="from_city_id" name="from_city_id" class="form-control select2">
                                                <option value="" selected>{{ __('levels.select') }}
                                                    {{ __('parcel.from_city') }}</option>
                                                @foreach (\App\Models\Backend\City::where('country_id', old('from_country_id', $delivery_charge->from_country_id))->get() as $fromcity)
                                                    <option value="{{ $fromcity->id }}" @selected(old('from_city_id', $delivery_charge->from_city_id) == $fromcity->id)>
                                                        {{ $fromcity->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('from_city_id')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group  col-lg-12">
                                            <label for="from_district_id">{{ __('parcel.from_district_id') }}</label> <span
                                                class="text-danger">*</span>
                                            <select id="from_district_id" name="from_district_id"
                                                class="form-control select2">
                                                <option value="" selected>{{ __('levels.select') }}
                                                    {{ __('parcel.from_district') }}</option>
                                                @foreach (\App\Models\Backend\District::where('city_id', old('from_city_id', $delivery_charge->from_city_id))->get() as $fromdistrict)
                                                    <option value="{{ $fromdistrict->id }}" @selected(old('from_district_id', $delivery_charge->from_district_id) == $fromdistrict->id)>
                                                        {{ $fromdistrict->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('from_district_id')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group  col-lg-12">
                                            <label for="from_town_id">{{ __('parcel.from_town_id') }}</label> <span
                                                class="text-danger">*</span>
                                            <select id="from_town_id" name="from_town_id" class="form-control select2">
                                                <option value="" selected>{{ __('levels.select') }}
                                                    {{ __('parcel.from_town') }}</option>

                                                @foreach (\App\Models\Backend\Town::where('district_id', old('from_district_id', $delivery_charge->from_district_id))->get() as $fromtown)
                                                    <option value="{{ $fromtown->id }}" @selected(old('from_town_id', $delivery_charge->from_town_id) == $fromtown->id)>
                                                        {{ $fromtown->name }}</option>
                                                @endforeach

                                            </select>
                                            @error('from_town_id')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>


                                        <div class="form-group  col-lg-12">
                                            <label for="from_portal_code">{{ __('parcel.from_portal_code') }}</label> <span
                                                class="text-danger">*</span>
                                            <input id="from_portal_code" type="text" name="from_portal_code"
                                                class="form-control" placeholder="{{ __('parcel.enter_portal_code') }}"
                                                value="{{ $delivery_charge->from_portal_code }}">
                                            @error('from_portal_code')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>

                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="row">
                                        <div class="form-group  col-lg-12">
                                            <label for="to_country_id">{{ __('parcel.to_country_id') }}</label> <span
                                                class="text-danger">*</span>
                                            <select id="to_country_id" name="to_country_id" class="form-control select2">
                                                <option value="" selected>{{ __('levels.select') }}
                                                    {{ __('parcel.to_country') }}</option>
                                                @foreach ($countries as $country)
                                                    <option value="{{ $country->id }}" @selected(old('to_country_id', $delivery_charge->to_country_id) == $country->id)>
                                                        {{ $country->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('to_country_id')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="form-group  col-lg-12">
                                            <label for="to_city_id">{{ __('parcel.to_city_id') }}</label> <span
                                                class="text-danger">*</span>
                                            <select id="to_city_id" name="to_city_id" class="form-control select2">
                                                <option value="">{{ __('levels.select') }}
                                                    {{ __('parcel.to_city') }}</option>
                                                @foreach (\App\Models\Backend\City::where('country_id', old('to_country_id', $delivery_charge->to_country_id))->get() as $tocity)
                                                    <option value="{{ $tocity->id }}" @selected(old('to_city_id', $delivery_charge->to_city_id) == $tocity->id)>
                                                        {{ $tocity->name }}</option>
                                                @endforeach

                                            </select>
                                            @error('to_city_id')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="form-group  col-lg-12">
                                            <label for="to_district_id">{{ __('parcel.to_district_id') }}</label> <span
                                                class="text-danger">*</span>
                                            <select id="to_district_id" name="to_district_id"
                                                class="form-control select2">
                                                <option value="" selected>{{ __('levels.select') }}
                                                    {{ __('parcel.to_district_id') }}</option>

                                                @foreach (\App\Models\Backend\District::where('city_id', old('to_city_id', $delivery_charge->to_city_id))->get() as $todistrict)
                                                    <option value="{{ $todistrict->id }}" @selected(old('to_district_id', $delivery_charge->to_district_id) == $todistrict->id)>
                                                        {{ $todistrict->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('to_district_id')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group  col-lg-12">
                                            <label for="to_town_id">{{ __('parcel.to_town_id') }}</label> <span
                                                class="text-danger">*</span>
                                            <select id="to_town_id" name="to_town_id" class="form-control select2">
                                                <option value="" selected>{{ __('levels.select') }}
                                                    {{ __('parcel.to_town_id') }}</option>

                                                @foreach (\App\Models\Backend\Town::where('district_id', old('to_district_id', $delivery_charge->to_district_id))->get() as $totown)
                                                    <option value="{{ $totown->id }}" @selected(old('to_town_id', $delivery_charge->to_town_id) == $totown->id)>
                                                        {{ $totown->name }}</option>
                                                @endforeach

                                            </select>
                                            @error('to_town_id')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="form-group  col-lg-12">
                                            <label for="to_portal_code">{{ __('parcel.to_portal_code') }}</label> <span
                                                class="text-danger">*</span>
                                            <input id="to_portal_code" type="text" name="to_portal_code"
                                                placeholder="{{ __('parcel.enter_portal_code') }}" class="form-control "
                                                value="{{ $delivery_charge->to_portal_code }}">
                                            @error('to_portal_code')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-lg-6">
                                    <label for="position">{{ __('levels.position') }} </label>
                                    <input id="position" type="number" name="position" data-parsley-trigger="change"
                                        placeholder="{{ __('placeholder.Enter_Position') }}" autocomplete="off"
                                        class="form-control  " value="{{ old('position', $delivery_charge->position) }}"
                                        require>
                                    @error('position')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>


                                <div class="form-group col-lg-6">
                                    <label for="status">{{ __('levels.status') }}</label> <span
                                        class="text-danger">*</span>
                                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                                        @foreach (trans('status') as $key => $status)
                                            <option value="{{ $key }}"
                                                {{ old('status', $delivery_charge->status) == $key ? 'selected' : '' }}>
                                                {{ $status }}</option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>

                            </div>



                            <div class="row">
                                <div class="col-lg-12">
                                    <h4>{{ __('parcel.door_to_door') }}</h4>

                                    <div class="row mt-2">
                                        <div class="form-group col-lg-4 col-xl-2">
                                            <label for="dtd_start_weight">{{ __('parcel.start_weight') }}</label> <span
                                                class="text-danger">*</span>
                                            <input id="dtd_start_weight" type="text" name="dtd_start_weight"
                                                data-parsley-trigger="change"
                                                placeholder="{{ __('parcel.enter_start_weight') }}" autocomplete="off"
                                                class="form-control"
                                                value="{{ old('dtd_start_weight', $delivery_charge->dtd_start_weight) }}"
                                                required>
                                            @error('dtd_start_weight')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="form-group col-lg-4 col-xl-2">
                                            <label for="dtd_end_weight">{{ __('parcel.end_weight') }}</label> <span
                                                class="text-danger">*</span>
                                            <input id="dtd_end_weight" type="text" name="dtd_end_weight"
                                                data-parsley-trigger="change"
                                                placeholder="{{ __('parcel.enter_end_weight') }}" autocomplete="off"
                                                class="form-control"
                                                value="{{ old('dtd_end_weight', $delivery_charge->dtd_end_weight) }}"
                                                required>
                                            @error('dtd_end_weight')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="form-group col-lg-4 col-xl-2">
                                            <label for="dtd_s_e_rate">{{ __('parcel.rate') }}
                                                ({{ settings()->currency }})</label> <span class="text-danger">*</span>
                                            <input id="dtd_s_e_rate" type="text" name="dtd_s_e_rate"
                                                data-parsley-trigger="change" placeholder="{{ __('parcel.enter_rate') }}"
                                                autocomplete="off" class="form-control"
                                                value="{{ old('dtd_s_e_rate', $delivery_charge->dtd_s_e_rate) }}" required>
                                            @error('dtd_s_e_rate')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="form-group col-lg-4 col-xl-2">
                                            <label
                                                for="dtd_additional_weight">{{ __('parcel.additional_weight') }}</label>
                                            <span class="text-danger">*</span>
                                            <input id="dtd_additional_weight" type="text" name="dtd_additional_weight"
                                                data-parsley-trigger="change"
                                                placeholder="{{ __('parcel.enter_additional_weight') }}"
                                                autocomplete="off" class="form-control"
                                                value="{{ old('dtd_additional_weight', $delivery_charge->dtd_additional_weight) }}"
                                                required>
                                            @error('dtd_additional_weight')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="form-group col-lg-4 col-xl-2">
                                            <label for="dtd_additional_rate">{{ __('parcel.additional_rate') }}
                                                ({{ settings()->currency }})</label> <span class="text-danger">*</span>
                                            <input id="dtd_additional_rate" type="text" name="dtd_additional_rate"
                                                data-parsley-trigger="change"
                                                placeholder="{{ __('parcel.enter_additional_rate') }}"
                                                autocomplete="off" class="form-control"
                                                value="{{ old('dtd_additional_rate', $delivery_charge->dtd_additional_rate) }}"
                                                required>
                                            @error('dtd_additional_rate')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>

                                    </div>
                                </div>


                                <div class=" col-lg-12">
                                    <h4>{{ __('parcel.door_to_hub') }}</h4>

                                    <div class="row mt-2">
                                        <div class="form-group col-lg-4 col-xl-2">
                                            <label for="dth_start_weight">{{ __('parcel.start_weight') }}</label> <span
                                                class="text-danger">*</span>
                                            <input id="dth_start_weight" type="text" name="dth_start_weight"
                                                data-parsley-trigger="change"
                                                placeholder="{{ __('parcel.enter_start_weight') }}" autocomplete="off"
                                                class="form-control"
                                                value="{{ old('dth_start_weight', $delivery_charge->dth_start_weight) }}"
                                                required>
                                            @error('dth_start_weight')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="form-group col-lg-4 col-xl-2">
                                            <label for="dth_end_weight">{{ __('parcel.end_weight') }}</label> <span
                                                class="text-danger">*</span>
                                            <input id="dth_end_weight" type="text" name="dth_end_weight"
                                                data-parsley-trigger="change"
                                                placeholder="{{ __('parcel.enter_end_weight') }}" autocomplete="off"
                                                class="form-control"
                                                value="{{ old('dth_end_weight', $delivery_charge->dth_end_weight) }}"
                                                required>
                                            @error('dth_end_weight')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="form-group col-lg-4 col-xl-2">
                                            <label for="dth_s_e_rate">{{ __('parcel.rate') }}
                                                ({{ settings()->currency }})</label> <span class="text-danger">*</span>
                                            <input id="dth_s_e_rate" type="text" name="dth_s_e_rate"
                                                data-parsley-trigger="change"
                                                placeholder="{{ __('parcel.enter_rate') }}" autocomplete="off"
                                                class="form-control"
                                                value="{{ old('dth_s_e_rate', $delivery_charge->dth_s_e_rate) }}"
                                                required>
                                            @error('dth_s_e_rate')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="form-group col-lg-4 col-xl-2">
                                            <label
                                                for="dth_additional_weight">{{ __('parcel.additional_weight') }}</label>
                                            <span class="text-danger">*</span>
                                            <input id="dth_additional_weight" type="text" name="dth_additional_weight"
                                                data-parsley-trigger="change"
                                                placeholder="{{ __('parcel.enter_additional_weight') }}"
                                                autocomplete="off" class="form-control"
                                                value="{{ old('dth_additional_weight', $delivery_charge->dth_additional_weight) }}"
                                                requiredd>
                                            @error('dth_additional_weight')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="form-group col-lg-4 col-xl-2">
                                            <label for="dth_additional_rate">{{ __('parcel.additional_rate') }}
                                                ({{ settings()->currency }})</label> <span class="text-danger">*</span>
                                            <input id="dth_additional_rate" type="text" name="dth_additional_rate"
                                                data-parsley-trigger="change"
                                                placeholder="{{ __('parcel.enter_additional_rate') }}"
                                                autocomplete="off" class="form-control"
                                                value="{{ old('dth_additional_rate', $delivery_charge->dth_additional_rate) }}"
                                                requiredd>
                                            @error('dth_additional_rate')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class=" col-lg-12">
                                    <h4>{{ __('parcel.hub_to_hub') }}</h4>

                                    <div class="row mt-2">
                                        <div class="form-group col-lg-4 col-xl-2">
                                            <label for="hth_start_weight">{{ __('parcel.start_weight') }}</label> <span
                                                class="text-danger">*</span>
                                            <input id="hth_start_weight" type="text" name="hth_start_weight"
                                                data-parsley-trigger="change"
                                                placeholder="{{ __('parcel.enter_start_weight') }}" autocomplete="off"
                                                class="form-control"
                                                value="{{ old('hth_start_weight', $delivery_charge->hth_start_weight) }}"
                                                required>
                                            @error('hth_start_weight')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="form-group col-lg-4 col-xl-2">
                                            <label for="hth_end_weight">{{ __('parcel.end_weight') }}</label> <span
                                                class="text-danger">*</span>
                                            <input id="hth_end_weight" type="text" name="hth_end_weight"
                                                data-parsley-trigger="change"
                                                placeholder="{{ __('parcel.enter_end_weight') }}" autocomplete="off"
                                                class="form-control"
                                                value="{{ old('hth_end_weight', $delivery_charge->hth_end_weight) }}"
                                                required>
                                            @error('hth_end_weight')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="form-group col-lg-4 col-xl-2">
                                            <label for="hth_s_e_rate">{{ __('parcel.rate') }}
                                                ({{ settings()->currency }})</label> <span class="text-danger">*</span>
                                            <input id="hth_s_e_rate" type="text" name="hth_s_e_rate"
                                                data-parsley-trigger="change"
                                                placeholder="{{ __('parcel.enter_rate') }}" autocomplete="off"
                                                class="form-control"
                                                value="{{ old('hth_s_e_rate', $delivery_charge->hth_s_e_rate) }}"
                                                required>
                                            @error('hth_s_e_rate')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="form-group col-lg-4 col-xl-2">
                                            <label
                                                for="hth_additional_weight">{{ __('parcel.additional_weight') }}</label>
                                            <span class="text-danger">*</span>
                                            <input id="hth_additional_weight" type="text" name="hth_additional_weight"
                                                data-parsley-trigger="change"
                                                placeholder="{{ __('parcel.enter_additional_weight') }}"
                                                autocomplete="off" class="form-control"
                                                value="{{ old('hth_additional_weight', $delivery_charge->hth_additional_weight) }}"
                                                requiredd>
                                            @error('hth_additional_weight')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="form-group col-lg-4 col-xl-2">
                                            <label for="hth_additional_rate">{{ __('parcel.additional_rate') }}
                                                ({{ settings()->currency }})</label> <span class="text-danger">*</span>
                                            <input id="hth_additional_rate" type="text" name="hth_additional_rate"
                                                data-parsley-trigger="change"
                                                placeholder="{{ __('parcel.enter_additional_rate') }}"
                                                autocomplete="off" class="form-control"
                                                value="{{ old('hth_additional_rate', $delivery_charge->hth_additional_rate) }}"
                                                requiredd>
                                            @error('hth_additional_rate')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class=" col-lg-12">
                                    <h4>{{ __('parcel.hub_to_door') }}</h4>

                                    <div class="row mt-2">
                                        <div class="form-group col-lg-4 col-xl-2">
                                            <label for="htd_start_weight">{{ __('parcel.start_weight') }} </label> <span
                                                class="text-danger">*</span>
                                            <input id="htd_start_weight" type="text" name="htd_start_weight"
                                                data-parsley-trigger="change"
                                                placeholder="{{ __('parcel.enter_start_weight') }}" autocomplete="off"
                                                class="form-control"
                                                value="{{ old('htd_start_weight', $delivery_charge->htd_start_weight) }}"
                                                required>
                                            @error('htd_start_weight')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="form-group col-lg-4 col-xl-2">
                                            <label for="htd_end_weight">{{ __('parcel.end_weight') }}</label> <span
                                                class="text-danger">*</span>
                                            <input id="htd_end_weight" type="text" name="htd_end_weight"
                                                data-parsley-trigger="change"
                                                placeholder="{{ __('parcel.enter_end_weight') }}" autocomplete="off"
                                                class="form-control"
                                                value="{{ old('htd_end_weight', $delivery_charge->htd_end_weight) }}"
                                                required>
                                            @error('htd_end_weight')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="form-group col-lg-4 col-xl-2">
                                            <label for="htd_s_e_rate">{{ __('parcel.rate') }}
                                                ({{ settings()->currency }})</label> <span class="text-danger">*</span>
                                            <input id="htd_s_e_rate" type="text" name="htd_s_e_rate"
                                                data-parsley-trigger="change"
                                                placeholder="{{ __('parcel.enter_rate') }}" autocomplete="off"
                                                class="form-control"
                                                value="{{ old('htd_s_e_rate', $delivery_charge->htd_s_e_rate) }}"
                                                required>
                                            @error('htd_s_e_rate')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="form-group col-lg-4 col-xl-2">
                                            <label
                                                for="htd_additional_weight">{{ __('parcel.additional_weight') }}</label>
                                            <span class="text-danger">*</span>
                                            <input id="htd_additional_weight" type="text" name="htd_additional_weight"
                                                data-parsley-trigger="change"
                                                placeholder="{{ __('parcel.enter_additional_weight') }}"
                                                autocomplete="off" class="form-control"
                                                value="{{ old('htd_additional_weight', $delivery_charge->htd_additional_weight) }}"
                                                requiredd>
                                            @error('htd_additional_weight')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="form-group col-lg-4 col-xl-2">
                                            <label for="htd_additional_rate">{{ __('parcel.additional_rate') }}
                                                ({{ settings()->currency }})</label> <span class="text-danger">*</span>
                                            <input id="htd_additional_rate" type="text" name="htd_additional_rate"
                                                data-parsley-trigger="change"
                                                placeholder="{{ __('parcel.enter_additional_rate') }}"
                                                autocomplete="off" class="form-control"
                                                value="{{ old('htd_additional_rate', $delivery_charge->htd_additional_rate) }}"
                                                requiredd>
                                            @error('htd_additional_rate')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 ">
                                    <button type="submit"
                                        class="btn btn-space btn-primary">{{ __('levels.save') }}</button>
                                    <a href="{{ route('delivery-charge.index') }}"
                                        class="btn btn-space btn-secondary">{{ __('levels.cancel') }}</a>
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
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @include('backend.delivery_charge.from_js')
    @include('backend.delivery_charge.to_js')
@endpush
