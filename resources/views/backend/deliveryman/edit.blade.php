@extends('backend.partials.master')
@section('title')
{{ __('deliveryman.title') }} {{ __('levels.edit') }}
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
                            <li class="breadcrumb-item"><a href="{{ route('deliveryman.index') }}"
                                    class="breadcrumb-link">{{ __('deliveryman.title') }}</a></li>
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
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('deliveryman.edit_deliveryman') }}</h4>
                </div>
                <div class="card-body">
                    <!-- <h2 class="pageheader-title">{{ __('deliveryman.edit_deliveryman') }}</h2> -->
                    <form action="{{ route('deliveryman.update', ['id' => $deliveryman->id]) }}" method="POST" enctype="multipart/form-data" id="basicform">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="Name">{{ __('levels.name') }}</label> <span class="text-danger">*</span>
                                    <input id="name" type="text" name="name" data-parsley-trigger="change" placeholder="{{ __('placeholder.Enter_name') }}" autocomplete="off" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $deliveryman->user->name) }}" require>
                                    @error('name')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group ">
                                    <label for="email">{{ __('levels.email') }}</label> <span class="text-danger">*</span>
                                    <input id="email" type="text" name="email" data-parsley-trigger="change" placeholder="{{ __('placeholder.enter_email') }}" autocomplete="off" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $deliveryman->user->email) }}">
                                    @error('email')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="opening_balance">{{ __('levels.opening_balance') }}</label>
                                    <input id="opening_balance" type="number" step="any" name="opening_balance" data-parsley-trigger="change" placeholder="{{ __('placeholder.Enter_opening_balance') }}" autocomplete="off" class="form-control @error('opening_balance') is-invalid @enderror" value="{{ old('opening_balance', $deliveryman->opening_balance) }}">
                                    @error('opening_balance')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="driver_type">{{ __('levels.type') }}</label> <span class="text-danger">*</span>
                                    <select name="driver_type" class="form-control select2" id="driver_type">
                                        <option value="">{{ __('levels.select') }} {{ __('levels.type') }}</option>
                                        <option value="{{ \App\Enums\DriverType::EMPLOYEE }}" @selected(old('driver_type',$deliveryman->driver_type) == \App\Enums\DriverType::EMPLOYEE)>{{ __('levels.employee') }}</option>
                                        <option value="{{ \App\Enums\DriverType::FREELANCER }}" @selected(old('driver_type',$deliveryman->driver_type) == \App\Enums\DriverType::FREELANCER)>{{ __('levels.freelancer') }}</option>
                                    </select>
                                    @error('driver_type')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6 driverEmployeeData {{ old('driver_type',$deliveryman->driver_type) == \App\Enums\DriverType::EMPLOYEE ? 'd-block' : 'd-none' }}">
                                <div class="form-group">
                                    <label for="salary">{{ __('levels.salary') }}</label>
                                    <input type="number" id="salary" class="form-control" placeholder="{{ __('salary.title') }}" name="salary" value="{{ $deliveryman->user->salary }}" />
                                    @error('salary')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6 driverFreelancerData {{ old('driver_type',$deliveryman->driver_type) == \App\Enums\DriverType::FREELANCER ? 'd-block' : 'd-none' }}">
                                <div class="form-group">
                                    <label for="pickup_charge">{{ __('levels.pickup_charge') }}</label>
                                    <input id="pickup_charge" type="number" step="any" name="pickup_charge" data-parsley-trigger="change" placeholder="{{ __('placeholder.Enter_pickup_charge') }}" autocomplete="off" class="form-control @error('pickup_charge') is-invalid @enderror" value="{{ old('pickup_charge', $deliveryman->pickup_charge) }}">
                                    @error('pickup_charge')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6 driverFreelancerData {{ old('driver_type',$deliveryman->driver_type) == \App\Enums\DriverType::FREELANCER ? 'd-block' : 'd-none' }}">
                                <div class="form-group">
                                    <label for="delivery_charge">{{ __('levels.delivery_charge') }}</label>
                                    <input id="delivery_charge" type="number" step="any" name="delivery_charge" data-parsley-trigger="change" placeholder="{{ __('placeholder.Enter_delivery_charge') }}" autocomplete="off" class="form-control @error('delivery_charge') is-invalid @enderror" value="{{ old('delivery_charge', $deliveryman->delivery_charge) }}">
                                    @error('delivery_charge')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6 driverFreelancerData {{ old('driver_type',$deliveryman->driver_type) == \App\Enums\DriverType::FREELANCER ? 'd-block' : 'd-none' }}">
                                <div class="form-group">
                                    <label for="return_charge">{{ __('levels.return_charge') }}</label>
                                    <input id="return_charge" type="number" step="any" name="return_charge" data-parsley-trigger="change" placeholder="{{ __('placeholder.Enter_return_charge') }}" autocomplete="off" class="form-control @error('return_charge') is-invalid @enderror" value="{{ old('return_charge', $deliveryman->return_charge) }}">
                                    @error('return_charge')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6 driverFreelancerData {{ old('driver_type',$deliveryman->driver_type) == \App\Enums\DriverType::FREELANCER ? 'd-block' : 'd-none' }}">
                                <div class="form-group">
                                    <label for="freelance_signed_contract" class="col-form-label text-sm-right">{{ __('levels.freelance_signed_contract') }}</label>
                                    <input name="freelance_signed_contract" value="{{ old('freelance_signed_contract',$deliveryman->freelance_signed_contract) }}" placeholder="{{ __('levels.freelance_signed_contract') }}" id="freelance_signed_contract" type="text" class="form-control   @error('freelance_signed_contract') is-invalid @enderror" />
                                    @error('freelance_signed_contract')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="current_balance">{{ __('levels.current_balance') }}</label>
                                    <input id="current_balance" type="number" step="any" name="current_balance" data-parsley-trigger="change" placeholder="{{ __('placeholder.Enter_opening_balance') }}" autocomplete="off" class="form-control @error('current_balance') is-invalid @enderror" value="{{ old('current_balance', $deliveryman->current_balance) }}">
                                    @error('current_balance')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-9">
                                            <label for="image_id">{{ __('levels.image') }}</label>
                                            <input id="image_id" type="file" name="image_id" data-parsley-trigger="change" autocomplete="off" class="form-control" require>
                                        </div>
                                        <div class="col-3">
                                            <img src="{{ $deliveryman->user->image }}" alt="user" class="rounded" width="75" height="75">
                                        </div>
                                    </div>
                                    @error('image_id')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="mobile">{{ __('levels.mobile') }}</label> <span class="text-danger">*</span><br>
                                    <div class="input-group">
                                        <div class="input-group-prepend" style="height: 39px !important;">
                                            <span class="input-group-text" id="mobile" style="border-top-left-radius: 6px !important;border-bottom-left-radius: 6px !important;">
                                                {{ settings()->country_code }}
                                            </span>
                                        </div>
                                        <input type="text" class="form-control form-control-one @error('mobile') is-invalid @enderror" style="
                                            border-radius: 0 !important;
                                            border-top-right-radius: 6px !important;
                                            border-bottom-right-radius: 6px !important;
                                            padding: 0.25rem 0.25rem !important;
                                            line-height: 1.5 !important;
                                            height: 39px !important;
                                            " name="mobile" placeholder="{{ __('levels.enter_mobile_number') }}" value="{{ old('mobile',$deliveryman->user->mobile) }}" id="mobile" autocomplete="off">
                                        <input type="hidden" id="country_code" name="country_code" value="{{ old('country_code', $deliveryman->user->CountryCode) }}" />
                                    </div>
                                    @error('mobile')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="email">{{ __('levels.password') }}</label>
                                    <input id="password" type="password" name="password" data-parsley-trigger="change" placeholder="{{ __('placeholder.Enter_password') }}" autocomplete="off" class="form-control @error('password') is-invalid @enderror" value="{{ old('password') }}">
                                    @error('password')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="status">{{ __('levels.status') }}</label> <span class="text-danger">*</span>
                                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                                        @foreach (trans('status') as $key => $status)
                                        <option value="{{ $key }}" {{ old('status', $deliveryman->user->status) == $key ? 'selected' : '' }}>
                                            {{ $status }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-9">
                                            <label for="driving_license_image_id">{{ __('levels.driving_license') }}</label>
                                            <input id="driving_license_image_id" type="file" name="driving_license_image_id" data-parsley-trigger="change" autocomplete="off" class="form-control" value="{{ old('driving_license_image_id') }}" require>
                                        </div>
                                        <div class="col-3">
                                            <img src="{{ $deliveryman->driving_license_image }}" alt="user" class="rounded" width="75" height="75">
                                        </div>
                                    </div>
                                    @error('driving_license_image_id')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="vehicle_type" class="col-form-label text-sm-right">Vehicle Type</label>
                                    <select name="vehicle_type" id="vehicle_type" class="form-control form-control-lg select2">
                                        <option value="Car" @selected(@$deliveryman->vehicle_type == 'Car')>Car</option>
                                        <option value="Van" @selected(@$deliveryman->vehicle_type == 'Van')>Van</option>
                                        <option value="Motorcycle" @selected(@$deliveryman->vehicle_type == 'Motorcycle')>Motorcycle</option>
                                        <option value="Truck" @selected(@$deliveryman->vehicle_type == 'Truck')>Truck</option>
                                        <option value="Other" @selected(@$deliveryman->vehicle_type == 'Other')>Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="hiring_date" class="col-form-label text-sm-right">{{ __('levels.hiring_date') }}</label>
                                    <input name="hiring_date" id="hiring_date" type="date" class="form-control @error('hiring_date') is-invalid @enderror" value="{{ old('hiring_date',$deliveryman->hiring_date) }}" />
                                    @error('hiring_date')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="internal_id_no" class="col-form-label text-sm-right">{{ __('levels.internal_id_no') }}</label>
                                    <input name="internal_id_no" id="internal_id_no" placeholder="{{ __('levels.internal_id_no') }}" type="text" class="form-control @error('internal_id_no') is-invalid @enderror" value="{{ old('internal_id_no',$deliveryman->internal_id_no) }}" />
                                    @error('internal_id_no')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="residence_address" class="col-form-label text-sm-right">{{ __('levels.residence_address') }}</label>
                                    <input name="residence_address" value="{{ old('residence_address',$deliveryman->residence_address) }}" id="residence_address" type="text" placeholder="{{ __('levels.residence_address') }}" class="form-control @error('residence_address') is-invalid @enderror" />
                                    @error('residence_address')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="nid_number" class="col-form-label text-sm-right">{{ __('levels.nid_number') }}</label>
                                    <input name="nid_number" id="nid_number" type="text" value="{{ old('nid_number',$deliveryman->nid_number) }}" placeholder="{{ __('levels.nid_number') }}" class="form-control @error('nid_number') is-invalid @enderror" />
                                    @error('nid_number')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-sm-9">
                                            <label for="front_side_scan">{{ __('levels.front_side_scan') }}</label>
                                            <input id="front_side_scan" type="file" name="front_side_scan" data-parsley-trigger="change" autocomplete="off" class="form-control @error('front_side_scan') is-invalid @enderror " value="{{ old('front_side_scan',$deliveryman->front_side_scan) }}">
                                            @error('front_side_scan')
                                            <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-sm-3">
                                            <img src="{{ @data_get($deliveryman->Allimage,'front_side_scan')}}" alt="user" class="rounded" width="75" height="75">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <label for="back_side_scan">{{ __('levels.back_side_scan') }}</label>
                                            <input id="back_side_scan" type="file" name="back_side_scan" data-parsley-trigger="change" autocomplete="off" class="form-control @error('back_side_scan') is-invalid @enderror " value="{{ old('back_side_scan',$deliveryman->back_side_scan) }}">
                                            @error('back_side_scan')
                                            <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <img src="{{ @data_get($deliveryman->Allimage,'back_side_scan')}}" alt="user" class="rounded" width="75" height="75">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="years_of_experience" class="col-form-label text-sm-right">{{ __('levels.years_of_experience') }}</label>
                                    <input name="years_of_experience" value="{{ old('years_of_experience',$deliveryman->years_of_experience) }}" placeholder="{{ __('levels.years_of_experience') }}" id="years_of_experience" type="text" class="form-control @error('years_of_experience') is-invalid @enderror" />
                                    @error('years_of_experience')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="social_security_no" class="col-form-label text-sm-right">{{ __('levels.social_security_no') }}</label>
                                    <input name="social_security_no" value="{{ old('social_security_no',$deliveryman->social_security_no) }}" id="social_security_no" placeholder="{{ __('levels.social_security_no') }}" type="text" class="form-control @error('social_security_no') is-invalid @enderror" />
                                    @error('social_security_no')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="year" class="col-form-label text-sm-right">{{ __('levels.year') }}</label>
                                    <input name="year" id="year" type="text" value="{{ old('year', $deliveryman->year) }}" placeholder="{{ __('levels.year') }}" class="form-control dateyear @error('year') is-invalid @enderror" />
                                    @error('year')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="registration_no" class="col-form-label text-sm-right">{{ __('levels.registration_no') }}</label>
                                    <input name="registration_no" value="{{ old('registration_no',$deliveryman->registration_no) }}" id="registration_no" placeholder="{{ __('levels.registration_no') }}" type="text" class="form-control   @error('registration_no') is-invalid @enderror" />
                                    @error('registration_no')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="chassis_no" class="col-form-label text-sm-right">{{ __('levels.chassis_no') }}</label>
                                    <input name="chassis_no" value="{{ old('chassis_no',$deliveryman->chassis_no) }}" id="chassis_no" type="text" placeholder="{{ __('levels.chassis_no') }}" class="form-control   @error('chassis_no') is-invalid @enderror" />
                                    @error('chassis_no')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <label for="regis_front_scan">{{ __('levels.regis_front_scan') }}</label>
                                            <input id="regis_front_scan" type="file" name="regis_front_scan" data-parsley-trigger="change" autocomplete="off" placeholder="{{ __('levels.regis_front_scan') }}" class="form-control @error('regis_front_scan') is-invalid @enderror " value="{{ old('regis_front_scan',$deliveryman->regis_front_scan) }}" require>
                                            @error('regis_front_scan')
                                            <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <img src="{{ @data_get($deliveryman->Allimage,'regis_front_scan')}}" alt="user" class="rounded" width="75" height="75">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <label for="regis_back_scan">{{ __('levels.regis_back_scan') }}</label>
                                            <input id="regis_back_scan" type="file" name="regis_back_scan" data-parsley-trigger="change" autocomplete="off" placeholder="{{ __('levels.regis_back_scan') }}" class="form-control @error('regis_back_scan') is-invalid @enderror " value="{{ old('regis_back_scan',$deliveryman->regis_back_scan) }}" require>
                                            @error('regis_back_scan')
                                            <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <img src="{{ @data_get($deliveryman->Allimage,'regis_back_scan')}}" alt="user" class="rounded" width="75" height="75">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="colour" class="col-form-label text-sm-right">{{ __('levels.colour') }}</label>
                                    <input name="colour" value="{{ old('colour',$deliveryman->colour) }}" id="colour" type="text" placeholder="{{ __('levels.colour') }}" class="form-control   @error('colour') is-invalid @enderror" />
                                    @error('colour')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <label for="inspctn_check_scan">{{ __('levels.inspctn_check_scan') }}</label>
                                            <input id="inspctn_check_scan" type="file" name="inspctn_check_scan" data-parsley-trigger="change" autocomplete="off" class="form-control @error('inspctn_check_scan') is-invalid @enderror " value="{{ old('inspctn_check_scan',$deliveryman->inspctn_check_scan) }}" require>
                                            @error('inspctn_check_scan')
                                            <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <img src="{{ @data_get($deliveryman->Allimage,'inspctn_check_scan')}}" alt="user" class="rounded" width="75" height="75">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="insurance_no" class="col-form-label text-sm-right">{{ __('levels.insurance_no') }}</label>
                                    <input name="insurance_no" value="{{ old('insurance_no',$deliveryman->insurance_no) }}" id="insurance_no" placeholder="{{ __('levels.insurance_no') }}" type="text" class="form-control   @error('insurance_no') is-invalid @enderror" />
                                    @error('insurance_no')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="insurance_company" class="col-form-label text-sm-right">{{ __('levels.insurance_company') }}</label>
                                    <input name="insurance_company" value="{{ old('insurance_company',$deliveryman->insurance_company) }}" id="insurance_company" placeholder="{{ __('levels.insurance_company') }}" type="text" class="form-control   @error('insurance_company') is-invalid @enderror" />
                                    @error('insurance_company')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="insurance_expiry_date" class="col-form-label text-sm-right">{{ __('levels.insurance_expiry_date') }}</label>
                                    <input name="insurance_expiry_date" value="{{ old('insurance_expiry_date',$deliveryman->insurance_expiry_date) }}" id="insurance_expiry_date" type="date" class="form-control @error('insurance_expiry_date') is-invalid @enderror" />
                                    @error('insurance_expiry_date')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <label for="insurance_crtfy_scan">{{ __('levels.insurance_crtfy_scan') }}</label>
                                            <input id="insurance_crtfy_scan" type="file" name="insurance_crtfy_scan" data-parsley-trigger="change" autocomplete="off" placeholder="{{ __('levels.insurance_crtfy_scan') }}" class="form-control @error('insurance_crtfy_scan') is-invalid @enderror " value="{{ old('insurance_crtfy_scan',$deliveryman->insurance_crtfy_scan) }}" require>
                                            @error('insurance_crtfy_scan')
                                            <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <img src="{{ @data_get($deliveryman->Allimage,'insurance_crtfy_scan')}}" alt="user" class="rounded" width="75" height="75">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="tech_control_id" class="col-form-label text-sm-right">{{ __('levels.tech_control_id') }}</label>
                                    <input name="tech_control_id" value="{{ old('tech_control_id',$deliveryman->tech_control_id) }}" placeholder="{{ __('levels.tech_control_id') }}" id="tech_control_id" type="text" class="form-control  @error('tech_control_id') is-invalid @enderror" />
                                    @error('tech_control_id')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="tech_c_expiry_date" class="col-form-label text-sm-right">{{ __('levels.tech_c_expiry_date') }}</label>
                                    <input name="tech_c_expiry_date" value="{{ old('tech_c_expiry_date',$deliveryman->tech_c_expiry_date) }}" id="tech_c_expiry_date" type="date" class="form-control @error('tech_c_expiry_date') is-invalid @enderror" />
                                    @error('tech_c_expiry_date')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <label for="tech_c_scan">{{ __('levels.tech_c_scan') }}</label>
                                            <input id="tech_c_scan" type="file" value="{{ old('tech_c_scan',$deliveryman->tech_c_scan) }}" name="tech_c_scan" data-parsley-trigger="change" autocomplete="off" class="form-control @error('tech_c_scan') is-invalid @enderror " value="{{ old('tech_c_scan') }}" require>
                                            @error('tech_c_scan')
                                            <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <img src="{{ @data_get($deliveryman->Allimage,'tech_c_scan')}}" alt="user" class="rounded" width="75" height="75">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="province_id" class="col-form-label text-sm-right">{{ __('parcel.province') }}<span class="text-danger">*</span></label>
                                    <select name="province_id" id="province_id" class="form-control form-control-lg select2" required>
                                        <option value="">{{ __('levels.select') }} {{ __('parcel.province') }}</option>
                                        @foreach (\App\Models\Backend\Province::all() as $province)
                                        <option value="{{ $province->id }}" @selected(old('province_id', $deliveryman->province_id) == $province->id)>
                                            {{ $province->name }}({{ $province->province_code }})
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="city_id" class="col-form-label text-sm-right">{{ __('parcel.city') }}<span class="text-danger">*</span></label>
                                    <select name="city_id" id="city_id" class="form-control form-control-lg select2" required>
                                        <option value="">{{ __('levels.select') }} {{ __('parcel.city') }}</option>
                                        @foreach (\App\Models\Backend\City::all() as $city)
                                        @if (old('province_id',$deliveryman->province_id) == $city->province_id)
                                        <option value="{{ $city->id }}" @selected(old('city_id', $deliveryman->city_id) == $city->id)>
                                            {{ $city->name }}
                                        </option>
                                        @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="driver_side_type" class="col-form-label text-sm-right">{{ __('levels.driver_side_type') }}<span class="text-danger">*</span></label>
                                    <select name="driver_side_type" id="driver_side_type" class="form-control form-control-lg select2" required>
                                        <option value="">{{ __('levels.select') }} {{ __('levels.driver_side_type') }}</option>
                                        <option value="{{ \App\Enums\DriverSideType::INSIDE }}" @selected(old('driver_side_type', $deliveryman->driver_side_type) == \App\Enums\DriverSideType::INSIDE)>
                                            {{ __('levels.inside') }}
                                        </option>
                                        <option value="{{ \App\Enums\DriverSideType::OUTSIDE }}" @selected(old('driver_side_type', $deliveryman->driver_side_type) == \App\Enums\DriverSideType::OUTSIDE)>
                                            {{ __('levels.outside') }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="pickup_location">Location</label> <span class="text-danger">*</span>
                                    <input type="hidden" id="pickup_lat" name="location_lat" required="" value="{{ old('location_lat', $deliveryman->user->latitude) }}">
                                    <input type="hidden" id="pickup_long" name="location_long" required="" value="{{ old('location_long', $deliveryman->user->longitude) }}">
                                    <div class="main-search-input-item location location-search">
                                        <div id="autocomplete-container" class="form-group random-search">
                                            <input id="autocomplete-input" type="text" name="location" class="recipe-search2 mb-2 form-control" value="{{ old('location', $deliveryman->user->location) }}" placeholder="Location Here!">
                                            <a href="javascript:void(0)" class="submit-btn btn current-location" id="locationIcon" onclick="getLocation()">
                                                <i class="fa fa-crosshairs"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="d-none">
                                        <div id="googleMap" class="custom-map"></div>
                                    </div>
                                    @error('location')
                                    <span class="text-danger mt-2">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="address" class="col-form-label text-sm-right">{{ __('levels.address') }}</label> <span class="text-danger">*</span>
                                    <textarea name="address" placeholder="{{ __('placeholder.Enter_address') }}" id="address" class="form-control @error('address') is-invalid @enderror">{{ old('address', $deliveryman->user->address) }}</textarea>
                                    @error('address')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 d-flex justify-content-end">
                                <a href="{{ route('deliveryman.index') }}" class="btn btn-space btn-secondary">{{ __('levels.cancel') }}</a>
                                <button type="submit" class="btn btn-space btn-primary">{{ __('levels.save_change') }}</button>
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css" />
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
</style>
@endpush
@push('scripts')
<script src="{{ static_asset('backend') }}/vendor/jquery/jquery-3.3.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();

        $("#driver_type").on('change', function() {
            var driver_type = $(this).val();
            if (driver_type == 1) {
                $(".driverEmployeeData").removeClass("d-none");
                $(".driverEmployeeData").addClass("d-block");

                $(".driverFreelancerData").removeClass("d-block");
                $(".driverFreelancerData").addClass("d-none");
            } else if (driver_type == 2) {
                $(".driverEmployeeData").removeClass("d-block");
                $(".driverEmployeeData").addClass("d-none");

                $(".driverFreelancerData").removeClass("d-none");
                $(".driverFreelancerData").addClass("d-block");
            }
        });

        $("#province_id").on('change', function() {
            var id = $(this).val();
            var op = "";
            $.ajax({
                type: "GET",
                url: "{{ url('admin/province/wise/city') }}/" + id,
                dataType: 'json',
                success: function(data) {
                    op += '<option  value="">--Select City--</option>';
                    for (var i = 0; i < data.length; i++) {
                        op += '<option  value="' + data[i].id + '">' + data[i].name + '</option>';
                    }
                    $('#city_id').html(op);
                }
            });
        });
    });

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

    $('.dateyear').datepicker({
        format: 'yyyy',
        viewMode: "years",
        minViewMode: "years"
    });
</script>
@endpush