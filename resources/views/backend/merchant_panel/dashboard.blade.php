<!-- wrapper  -->
@extends('backend.partials.master')
@section('title')
    {{ __('merchant.dashboard') }}
@endsection
@section('maincontent')
    <div class="container-fluid dashboard-content ">
        <!-- pageheader  -->
        <div class="row" style="margin-bottom: 35px;">
            <div class="col-xl-6 col-lg-6 col-md-6 col-12">
                <div class="welcome-text">
                    <h3>Welcome To Merchant Panel</h3>
                </div>
                <div class="page-header mb-0">
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mt-0 pt-0">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}" class="breadcrumb-link">{{ __('merchant.dashboard') }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ __('merchant.merchant_dashboard') }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        @if (empty(auth()->user()->email_verified_at) || empty(auth()->user()->mobile_verified_at))
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="card">
                        <div class="card-body">
                            <h3>Please verify your account</h3>
                            <div style="margin-left: 20px;">
                                <p class="p-0">
                                    @if (auth()->user()->email_verified_at)
                                        <span class="text-success">
                                            <i class="fas fa-check-circle fa-2x" style="position: absolute; left: 0;padding-left: 20px;"></i>
                                        </span>
                                    @else
                                        <span class="text-danger">
                                            <i class="fa fa-times fa-2x" style="position: absolute; left: 0;padding-left: 20px;"></i>
                                        </span>
                                    @endif
                                    <span style="margin-left: 15px;">Email address verify
                                        @if (empty(auth()->user()->email_verified_at))
                                            <a href="{{ route('resend.email.verify', auth()->user()->id) }}" class="btn btn-success btn-xs">Click Resend</a>
                                        @endif
                                    </span>
                                </p>
                                <p class="p-0">
                                    @if (auth()->user()->mobile_verified_at)
                                        <span class="text-success">
                                            <i class="fas fa-check-circle fa-2x" style="position: absolute;left: 0;padding-left: 20px;"></i>
                                        </span>
                                    @else
                                        <span class="text-danger">
                                            <i class="fa fa-times fa-2x" style="position: absolute;left: 0;padding-left: 20px;"></i>
                                        </span>
                                    @endif
                                    <span style="margin-left: 15px;">Phone number verify
                                        @if (empty(auth()->user()->mobile_verified_at))
                                            <a href="{{ route('phone.verify', auth()->user()->id) }}" class="btn btn-success btn-xs">Click Resend</a>
                                        @endif
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif (auth()->user()->document_status == 0)
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="card">
                        <div class="card-body">
                            @if (auth()->user()->submit_status == 1)
                                <div class="alert alert-danger" role="alert">
                                    Under review your account, please contact Admin!
                                </div>
                            @endif
                            <form action="{{ route('customer.document.submit') }}"  method="POST" enctype="multipart/form-data" id="basicform">
                                @csrf
                                <input type="hidden" name="merchant_id" id="merchant_id" value="{{ $merchant->id }}">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="nid">{{ __('levels.nid') }}<span class="text-danger">*</span> (Front Side)</label>
                                            <input id="nid" type="file" name="nid" data-parsley-trigger="change" autocomplete="off" class="form-control @error('nid') is-invalid @enderror" value="{{ old('nid') }}" require>
                                            @error('nid')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                            @if ($merchant->nid)
                                                <a href="{{ $merchant->nid }}" target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" title="Preview Nid Front Side">
                                                    <img src="{{ $merchant->nid }}" alt="user" class="mt-2" width="100" height="100">
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    @if ($merchant->account_type == 2)
                                        <div class="col-12 col-md-6">
                                            <div class="form-group">
                                                <label for="nid_back">{{ __('levels.nid') }}<span class="text-danger">*</span> (Back Side)</label>
                                                <input id="nid" type="file" name="nid_back" data-parsley-trigger="change" autocomplete="off" class="form-control" value="{{ old('nid_back') }}" require>
                                                @error('nid_back')
                                                    <small class="text-danger mt-2">{{ $message }}</small>
                                                @enderror
                                                @if ($merchant->nid_back)
                                                    <a href="{{ $merchant->nid_back }}" target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" title="Preview Nid Back Side">
                                                        <img src="{{ $merchant->nid_back }}" alt="user" class="mt-2" width="100" height="100">
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                    <div class="col-12 col-md-6 business">
                                        <div class="form-group">
                                            <label for="trade_license">{{ __('levels.trade_license') }}<span class="text-danger">*</span></label>
                                            <input id="trade_license" type="file" name="trade_license" data-parsley-trigger="change" autocomplete="off" class="form-control @error('trade_license') is-invalid @enderror" >
                                            @error('trade_license')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                            @if($merchant->trade)
                                                <a href="{{ $merchant->trade }}" target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" title="Preview Trade License">
                                                    <img src="{{ $merchant->trade }}" alt="user" class="mt-2" width="100" height="100">
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="image">{{ __('levels.profile_picture') }}<span class="text-danger">*</span></label>
                                            <input id="image_id" type="file" name="image_id" data-parsley-trigger="change" autocomplete="off" class="form-control @error('image_id') is-invalid @enderror">
                                            @error('image_id')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                            @if($merchant->user->image)
                                                <a href="{{ $merchant->user->image }}" target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" title="Preview Profile Picture">
                                                    <img src="{{ $merchant->user->image }}" alt="user" class="mt-2" width="100" height="100">
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="form-group">
                                            <label for="contract_document">{{ __('levels.contract_document') }}
                                                @if ($merchant->my_contract_document)
                                                    <a href="{{ $merchant->my_contract_document }}" target="_blank" class="text-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="Preview Contract Document">(View File)</a>
                                                @endif
                                            </label>
                                            <input type="file" id="contract_document" name="contract_document" class="form-control" accept="*">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-space btn-primary float-right">{{ __('levels.submit') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @elseif (auth()->user()->status == 0)
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="card">
                        <div class="card-body">
                            <div style="margin-left: 20px;">
                                <p class="p-0">
                                    @if (auth()->user()->status == 0)
                                        <span class="text-success"><i class="fas fa-check-circle fa-2x" style="position: absolute; left: 0;padding-left: 20px;"></i></span>
                                    @else
                                        <span class="text-danger"><i class="fa fa-times fa-2x" style="position: absolute; left: 0;padding-left: 20px;"></i></span>
                                    @endif
                                    <span style="margin-left: 15px;">Under review your account</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="endageheader">
            @if (!empty(auth()->user()->email_verified_at) && !empty(auth()->user()->mobile_verified_at) && auth()->user()->status == 1)
                <!-- end pageheader  -->
                <div class="ecommerce-widget merchant-dashboard-filter">
                    {{-- parcel info --}}
                    <div class="row">
                        <div class="col-12">
                            <div class="header__summery grid-3">
                                <!-- total parcel -->
                                <a href="{{ route('merchant-panel.parcel.index') }}">
                                    <div class="card metric-card">
                                        <div class="card-body">
                                            <div class="d-flex align-content-center gap-3">
                                                <label class="icon">
                                                    <i class="fa fa-box-open text-primary"></i>
                                                </label>
                                                <div class="box-content w-100 text-left">
                                                    <h5 class="title">{{ __('dashboard.total_parcel') }}</h5>
                                                    <div class="count">
                                                        {{ $t_parcel }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <!-- total unassigned -->
                                <a href="{{ route('merchant-panel.parcel.filter', ['parcel_status' => \App\Enums\ParcelStatus::PENDING]) }}">
                                    <div class="card metric-card">
                                        <div class="card-body">
                                            <div class="d-flex align-content-center gap-3">
                                                <label class="icon">
                                                    <i class="fa fa-hourglass-end text-primary"></i>
                                                </label>
                                                <div class="box-content w-100 text-left">
                                                    <h5 class="title">{{ __('dashboard.total_unassigned') }}</h5>
                                                    <div class="count">
                                                        {{ $t_pending }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <!-- total assigned -->
                                <a href="{{ route('merchant-panel.parcel.filter', ['parcel_status' => \App\Enums\ParcelStatus::DELIVERY_MAN_ASSIGN]) }}">
                                    <div class="card metric-card">
                                        <div class="card-body">
                                            <div class="d-flex align-content-center gap-3">
                                                <label class="icon">
                                                    <i class="fa fa-shipping-fast text-primary"></i>
                                                </label>
                                                <div class="box-content w-100 text-left">
                                                    <h5 class="title">{{ __('dashboard.total_assigned') }}</h5>
                                                    <div class="count">
                                                        {{ $t_assigned }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <!-- total processing -->
                                <a href="{{ route('merchant-panel.parcel.filter', ['parcel_status' => \App\Enums\ParcelStatus::PROCESSING]) }}">
                                    <div class="card metric-card">
                                        <div class="card-body">
                                            <div class="d-flex align-content-center gap-3">
                                                <label class="icon">
                                                    <i class="fa fa-circle-stop text-primary"></i>
                                                </label>
                                                <div class="box-content w-100 text-left">
                                                    <h5 class="title">{{ __('dashboard.total_processing') }}</h5>
                                                    <div class="count">
                                                        {{ $t_processing }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <!-- total delivered -->
                                <a href="{{ route('merchant-panel.parcel.filter', ['parcel_status' => \App\Enums\ParcelStatus::DELIVERED]) }}">
                                    <div class="card metric-card">
                                        <div class="card-body">
                                            <div class="d-flex align-content-center gap-3">
                                                <label class="icon">
                                                    <i class="fa fa-shipping-fast text-primary"></i>
                                                </label>
                                                <div class="box-content w-100 text-left">
                                                    <h5 class="title">{{ __('dashboard.total_deliverd') }}</h5>
                                                    <div class="count">
                                                        {{ $t_delivered }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                                <!-- total failure -->
                                <a href="{{ route('merchant-panel.parcel.filter', ['parcel_status' => \App\Enums\ParcelStatus::DELIVERY_FAILURE]) }}">
                                    <div class="card metric-card">
                                        <div class="card-body">
                                            <div class="d-flex align-content-center gap-3">
                                                <label class="icon">
                                                    <i class="fa fa-dna text-primary"></i>
                                                </label>
                                                <div class="box-content w-100 text-left">
                                                    <h5 class="title">{{ __('dashboard.total_failure') }}</h5>
                                                    <div class="count">
                                                        {{ $t_failure }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row merchant-panel header-summery d-none">
                        <div class="col-sm-6  col-lg-6 col-xl-4">
                            <a href="{{ route('merchant-panel.parcel.index') }}">
                                <div class="card border-3 border-top border-top-primary">
                                    <div class="card-body">
                                        <div class="d-flex ">
                                            <label class="icon p-10px"><i class="fa fa-box-open text-primary"></i></label>
                                            <div class="pl-2 w-100">
                                                <h5 class="m-0 text-primary">{{ __('dashboard.total_parcel') }}</h5>
                                                <h1 class="mb-1 m-0 text-primary">{{ $t_parcel }}</h1>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class=" col-sm-6 col-lg-6  col-xl-4">
                            <a href="{{ route('merchant-panel.parcel.filter', ['parcel_status' => \App\Enums\ParcelStatus::PENDING]) }}">
                                <div class="card border-3 border-top border-top-primary">
                                    <div class="card-body">
                                        <div class="d-flex ">
                                            <label class="icon  p-10px"><i class="fa fa-hourglass-end text-primary"></i></label>
                                            <div class="pl-2 w-100">
                                                <h5 class=" m-0 text-primary">{{ __('dashboard.total_unassigned') }}</h5>
                                                <h1 class="mb-1 m-0 text-primary">{{ $t_pending }}
                                                </h1>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class=" col-sm-6 col-lg-6  col-xl-4">
                            <a href="{{ route('merchant-panel.parcel.filter', ['parcel_status' => \App\Enums\ParcelStatus::DELIVERY_MAN_ASSIGN]) }}">
                                <div class="card border-3 border-top border-top-primary">
                                    <div class="card-body">
                                        <div class="d-flex ">
                                            <label class="icon  p-10px"><i class="fa fa-shipping-fast text-primary"></i></label>
                                            <div class="pl-2 w-100">
                                                <h5 class=" m-0 text-primary">{{ __('dashboard.total_assigned') }}</h5>
                                                <h1 class="mb-1 m-0 text-primary">{{ $t_assigned }}
                                                </h1>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class=" col-sm-6 col-lg-6 col-xl-4">
                            <a href="{{ route('merchant-panel.parcel.filter', ['parcel_status' => \App\Enums\ParcelStatus::PROCESSING]) }}">
                                <div class="card border-3 border-top border-top-primary">
                                    <div class="card-body">
                                        <div class="d-flex ">
                                            <label class="icon  p-10px"><i class="fa fa-circle-stop text-primary"></i></label>
                                            <div class="pl-2 w-100">
                                                <h5 class=" m-0 text-primary">{{ __('dashboard.total_processing') }}</h5>
                                                <h1 class="mb-1 m-0 text-primary">{{ $t_processing }}
                                                </h1>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-6 col-lg-6  col-xl-4">
                            <a href="{{ route('merchant-panel.parcel.filter', ['parcel_status' => \App\Enums\ParcelStatus::DELIVERED]) }}">
                                <div class="card border-3 border-top border-top-primary">
                                    <div class="card-body">
                                        <div class="d-flex">
                                            <label class="icon  p-10px"><i class="fa fa-shipping-fast text-primary"></i></label>
                                            <div class="pl-2 w-100">
                                                <h5 class=" m-0 text-primary">{{ __('dashboard.total_deliverd') }}</h5>
                                                <h1 class="mb-1 m-0 text-primary">{{ $t_delivered }}</h1>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class=" col-sm-6 col-lg-6 col-xl-4">
                            <a href="{{ route('merchant-panel.parcel.filter', ['parcel_status' => \App\Enums\ParcelStatus::DELIVERY_FAILURE]) }}">
                                <div class="card border-3 border-top border-top-primary">
                                    <div class="card-body">
                                        <div class="d-flex ">
                                            <label class="icon  p-10px"><i class="fa fa-dna text-primary"></i></label>
                                            <div class="pl-2 w-100">
                                                <h5 class=" m-0 text-primary">{{ __('dashboard.total_failure') }}</h5>
                                                <h1 class="mb-1 m-0 text-primary">{{ $t_failure }}</h1>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    <!-- end wrapper  -->
@endsection()
@push('scripts')
    <script>
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
@endpush
