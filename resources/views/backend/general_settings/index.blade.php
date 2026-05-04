@extends('backend.partials.master')
@section('title')
{{ __('menus.general_settings') }}
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
                            <li class="breadcrumb-item"><a href="{{route('dashboard.index')}}" class="breadcrumb-link">{{ __('dashboard.title') }}</a></li>
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link active">{{ __('menus.settings') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{route('general-settings.index')}}" class="breadcrumb-link active">{{__('menus.general_settings')}}</a></li>
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
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('menus.general_settings') }}</h4>
                </div>
                <div class="card-body">
                    <!-- <p class="h3">{{__('menus.general_settings')}}</h2> -->
                    <form action="{{route('general-settings.update')}}" method="POST" enctype="multipart/form-data" id="basicform">
                        @method('PUT')
                        @csrf
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="name">{{ __('levels.application_name') }}</label>
                                    <input id="name" type="text" name="name" data-parsley-trigger="change" placeholder="{{ __('placeholder.Enter_name') }}" autocomplete="off" class="form-control @error('name') is-invalid @enderror" value="{{ $settings->name }}" require>
                                    @error('name')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="phone">{{ __('levels.phone') }}</label>
                                    <input id="phone" type="text" name="phone" data-parsley-trigger="change" placeholder="{{ __('placeholder.Enter_phone') }}" autocomplete="off" class="form-control @error('phone') is-invalid @enderror" value="{{ $settings->phone }}" require>
                                    @error('phone')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group pt-3">
                                    <label for="email">{{ __('levels.email') }}</label>
                                    <input id="email" type="text" name="email" data-parsley-trigger="change" placeholder="{{ __('placeholder.enter_email') }}" autocomplete="off" class="form-control @error('email') is-invalid @enderror" value="{{ $settings->email }}" require>
                                    @error('email')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group pt-3">
                                    <label for="address">{{ __('levels.address') }}</label>
                                    <textarea id="address" type="text" name="address" data-parsley-trigger="change" placeholder="{{ __('placeholder.enter_address') }}" autocomplete="off" class="form-control @error('address') is-invalid @enderror" require>{{ $settings->address }}</textarea>
                                    @error('address')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group pt-3">
                                    <label for="about">{{ __('levels.about') }}</label>
                                    <textarea id="about" type="text" name="about" data-parsley-trigger="change" placeholder="{{ __('placeholder.enter_about') }}" autocomplete="off" class="form-control @error('about') is-invalid @enderror" require>{{ $settings->about }}</textarea>
                                    @error('about')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="currency">{{ __('levels.currency') }}</label>
                                    <select class="form-control @error('currency') is-invalid @enderror" id="currency" name="currency" required>
                                        <option value="" selected disabled>Select Currency</option>
                                        @forelse ($currencies as $currency)
                                        <option value="{{ $currency->symbol }}" {{$settings->currency == $currency->symbol ? 'selected' : ''}}>{{ @$currency->name }} {{ @$currency->symbol }}</option>
                                        @empty
                                        <option value="&#36;" {{$settings->currency == '$' ? 'selected' : ''}}>Dollar &#36;</option>
                                        @endforelse
                                    </select>
                                    @error('currency')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="currency">{{ __('settings.parcel_tracking') }} {{ __('levels.prefix') }}</label>
                                            <input type="text" name="par_track_prefix" class="form-control" placeholder="Enter Parcel Tracking Prefix" value="{{  @\Illuminate\Support\Str::upper($settings->par_track_prefix) }}" />
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="currency">{{ __('settings.parcel_invoice') }} {{ __('levels.prefix') }}</label>
                                            <input type="text" name="order_invoice_prefix" class="form-control" placeholder="Enter Parcel Tracking Prefix" value="{{  @\Illuminate\Support\Str::upper($settings->order_invoice_prefix) }}" />
                                        </div>
                                    </div> -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="max_active_parcels_per_rider">Max active parcels per rider</label>
                                            <input id="max_active_parcels_per_rider" type="number" min="1" name="max_active_parcels_per_rider" class="form-control" value="{{ $settings->max_active_parcels_per_rider ?? 1 }}" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="rider_min_withdrawal_amount">Rider minimum withdrawal amount</label>
                                            <input id="rider_min_withdrawal_amount" type="number" step="0.01" min="0" name="rider_min_withdrawal_amount" class="form-control" value="{{ $settings->rider_min_withdrawal_amount ?? 500 }}" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="marketplace_commission_percent">Marketplace commission (%)</label>
                                            <input id="marketplace_commission_percent" type="number" step="0.01" min="0" max="100" name="marketplace_commission_percent" class="form-control" value="{{ $settings->marketplace_commission_percent ?? 0 }}" />
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="currency">{{ __('invoice.invoice') }} {{ __('levels.prefix') }}</label>
                                            <input type="text" name="invoice_prefix" class="form-control" placeholder="Enter Invoice Prefix" value="{{  @\Illuminate\Support\Str::upper($settings->invoice_prefix) }}" />
                                        </div>
                                    </div> -->

                                    <!-- <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="primary_color">{{ __('levels.primary_color') }}</label>
                                            <input id="primary_color" type="color" name="primary_color" placeholder="Enter favicon" autocomplete="off" class="form-control @error('primary_color') is-invalid @enderror" value="{{ old('primary_color',settings()->primary_color) }}" require>
                                            @error('favicon')
                                            <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="text_color">{{ __('levels.text_color') }}</label>
                                            <input id="text_color" type="color" name="text_color" placeholder="Enter favicon" autocomplete="off" class="form-control @error('text_color') is-invalid @enderror" value="{{ old('text_color',settings()->text_color) }}" require>
                                            @error('text_color')
                                            <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div> -->

                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="copyright">{{ __('levels.copyright') }}</label>
                                    <input id="copyright" type="text" name="copyright" data-parsley-trigger="change" placeholder="{{ __('placeholder.Enter_copyright') }}" autocomplete="off" class="form-control @error('copyright') is-invalid @enderror" value="{{ $settings->copyright }}" require>
                                    @error('copyright')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-12">
                                            <label for="logo">{{ __('levels.logo') }}</label>
                                            <input id="logo" type="file" name="logo" data-parsley-trigger="change" placeholder="Enter logo" autocomplete="off" class="form-control @error('logo') is-invalid @enderror" value="{{ old('logo') }}" require>
                                            @error('logo')
                                            <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-6 ">
                                            <img src="{{$settings->logo_image}}" alt="user" class="rounded mt-3" width="100%" style="object-fit: contain; max-width: 200px;">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group pt-3">
                                    <div class="row">
                                        <div class="col-9">
                                            <label for="favicon">{{ __('levels.favicon') }}</label>
                                            <input id="favicon" type="file" name="favicon" data-parsley-trigger="change" placeholder="Enter favicon" autocomplete="off" class="form-control @error('favicon') is-invalid @enderror" value="{{ old('favicon') }}" require>
                                            @error('favicon')
                                            <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-3  ">
                                            <img src="{{$settings->favicon_image}}" alt="user" class="rounded mt-4" width="40" style="object-fit: contain">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group pt-3">
                                    <div class="row">
                                        <div class="col-9">
                                            <label for="mobile_app_logo">Mobile App Logo</label>
                                            <input id="mobile_app_logo" type="file" name="mobile_app_logo" data-parsley-trigger="change" autocomplete="off" class="form-control @error('mobile_app_logo') is-invalid @enderror">
                                            <small class="text-muted d-block mt-1">
                                                Recommended: square PNG, 1024x1024 (minimum 512x512, 1:1 ratio).
                                            </small>
                                            @error('mobile_app_logo')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-3">
                                            <img src="{{ $settings->mobile_app_logo_image }}" alt="mobile-app-logo" class="rounded mt-4" width="60" style="object-fit: contain">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="country">{{ __('levels.country') }}</label>
                                            <select class="form-control @error('country') is-invalid @enderror" id="country" name="country" required>
                                                <option value="" selected disabled>Select Country</option>
                                                @forelse (App\Models\Backend\Country::all() as $country)
                                                <option value="{{ $country->id }}" {{$settings->default_country == $country->id ? 'selected' : ''}}>{{ @$country->name }}</option>
                                                @empty

                                                @endforelse
                                            </select>
                                            @error('country')
                                            <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="currency"> {{ __('levels.country_code') }}</label>
                                            <input type="text" name="country_code" class="form-control" placeholder="Enter country code" value="{{  $settings->country_code }}" />
                                        </div>
                                    </div>
                                </div>

                            </div>
                            @if(hasPermission('general_settings_update'))
                            <div class="row pt-4">
                                <div class="col-12 text-right">
                                    <button type="submit" class="btn btn-space btn-primary">{{ __('levels.save_change') }}</button>
                                </div>
                            </div>
                            @endif
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