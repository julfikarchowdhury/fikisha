@extends('backend.partials.master')
@section('title')
    {{ __('sender_customer.title') }} {{ __('levels.add') }}
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
                            <li class="breadcrumb-item"><a href="{{route('dashboard.index')}}" class="breadcrumb-link">{{ __('levels.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sender_customers.index', $sender_id) }}" class="breadcrumb-link">{{ __('sender_customer.title') }}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{ __('levels.create') }}</a></li>
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
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('sender_customer.sender_customer_add') }}</h4>
                </div>
                <div class="card-body">
                    <!-- <h2 class="pageheader-title">{{ __('sender_customer.sender_customer_add') }}</h2> -->
                    <form action="{{ route('sender_customers.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="province_id">{{ __('levels.province') }}<span class="text-danger">*</span></label>
                                    <select id="province_id" name="province_id" class="form-control select2">
                                        <option value="">{{ __('levels.select') }} {{ __('levels.province') }}</option>
                                        @foreach (\App\Models\Backend\Province::all() as $province)
                                            <option value="{{ $province->id }}" @selected(old('province_id') == $province->id)>
                                                {{ $province->name }}({{ $province->province_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('province_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="city_id">{{ __('city.title') }}<span class="text-danger">*</span></label>
                                    <select id="city_id" name="city_id" class="form-control select2">
                                        <option value="">{{ __('levels.select') }} {{ __('city.title') }}</option>
                                        @foreach (\App\Models\Backend\City::all() as $city)
                                            @if (old('province_id') == $city->province_id)
                                                <option value="{{ $city->id }}" @selected(old('city_id') == $city->id)>
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
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="account_type">Account Type<span class="text-danger">*</span></label>
                                    <select class="form-control select2" name="account_type" id="account_type">
                                        <option value="">Select Account Type</option>
                                        <option value="1" @if (old('account_type') == 1) selected @endif>Individual</option>
                                        <option value="2" @if (old('account_type') == 2) selected @endif>Business</option>
                                    </select>
                                    @error('account_type')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="first_name">{{ __('levels.first_name') }}</label> <span class="text-danger">*</span>
                                    <input id="first_name" type="text" name="first_name" placeholder="{{ __('placeholder.Enter_first_name') }}" autocomplete="off" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}">
                                    <input type="hidden" name="sender_id" value="{{ old('sender_id', $sender_id) }}">
                                    @error('first_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="last_name">{{ __('levels.last_name') }}</label> <span class="text-danger">*</span>
                                    <input id="last_name" type="text" name="last_name" placeholder="{{ __('placeholder.Enter_last_name') }}" autocomplete="off" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}">
                                    @error('last_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="phone_number">{{ __('levels.phone_number') }}</label> <span class="text-danger">*</span>
                                    <input id="phone_number" type="number" name="phone_number" placeholder="{{ __('placeholder.enter_phone_number') }}" autocomplete="off" class="form-control @error('phone_number') is-invalid @enderror" value="{{ old('phone_number') }}">
                                    <input id="country_code" type="hidden" name="country_code" value="{{ old('country_code') }}">
                                    @error('phone_number')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="email">{{ __('levels.email') }}</label>
                                    <input id="email" type="text" name="email" placeholder="{{ __('placeholder.enter_email') }}" autocomplete="off" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="whatsapp_number">{{ __('levels.whatsapp_number') }}</label>
                                    <input id="whatsapp_number" type="number" name="whatsapp_number" placeholder="{{ __('placeholder.enter_whatsapp_number') }}" autocomplete="off" class="form-control @error('email') is-invalid @enderror" value="{{ old('whatsapp_number') }}">
                                    @error('whatsapp_number')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="status">{{ __('levels.status') }}</label> <span class="text-danger">*</span>
                                    <select name="status" class="form-control @error('status') is-invalid @enderror select2">
                                        @foreach (trans('status') as $key => $status)
                                            <option value="{{ $key }}" {{ old('status', \App\Enums\Status::ACTIVE) == $key ? 'selected' : '' }}>
                                                {{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="address">{{ __('levels.address') }} <span class="text-danger">*</span></label>
                                    <textarea name="address" id="address" class="form-control" rows="5" placeholder="{{ __('placeholder.Enter_address') }}" autocomplete="off"></textarea>
                                    @error('address')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                <button type="submit" class="btn btn-space btn-primary">{{ __('levels.save') }}</button>
                                <a href="{{ route('sender_customers.index', $sender_id) }}" class="btn btn-space btn-secondary">{{ __('levels.cancel') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- end basic form -->
    </div>
</div>
@endsection()
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.6/css/intlTelInput.css" />
    <style>
        #phone_number{
            padding-left: 90px !important;
        }
        #whatsapp_number{
            padding-left: 90px !important;
        }
        .iti {
            width: 100% !important;
        }
    </style>
@endpush
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.6/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.1.6/js/utils.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $(".select2").select2();

            var input = document.querySelector("#phone_number");
            window.intlTelInput(input,{ 
                initialCountry: '{{ settings()->defaultCountry->code }}',
                onlyCountries: ['{{ settings()->defaultCountry->code }}'],
                separateDialCode: true,
            });

            var iti = window.intlTelInputGlobals.getInstance(input);
            input.addEventListener('countrychange', function() {
            var countryCode = iti.getSelectedCountryData().dialCode;
                document.getElementById('country_code').value = '+'+countryCode;
            });
            var countryCode = iti.getSelectedCountryData().dialCode;
            document.getElementById('country_code').value = '+'+countryCode;

            var whatsapp_number = document.querySelector("#whatsapp_number");
            window.intlTelInput(whatsapp_number,{ 
                initialCountry: '{{ settings()->defaultCountry->code }}',
                onlyCountries: ['{{ settings()->defaultCountry->code }}'],
                separateDialCode: true,
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
        });
    </script>
@endpush

