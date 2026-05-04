@extends('backend.partials.master')
@section('title')
{{ __('menus.dashboard') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content ">
    <!-- pageheader  -->
    <div class="row g-3" style="margin-bottom: 35px;">
        <div class="col-xl-6 col-lg-6 col-md-6 col-12">
            <div class="welcome-text">
                @if (auth()->user()->hub_id)
                <h3>Welcome to {{ auth()->user()->hub->name }} Hub</h3>
                @else
                <h3>Welcome to {{ auth()->user()->my_user_type }}</h3>
                @endif
            </div>
            <div class="page-header mb-0">
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mt-0 pt-0">
                            <li class="breadcrumb-item"><a href="{{url('/')}}" class="breadcrumb-link">{{ __('menus.dashboard') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{settings()->name }} {{ __('menus.dashboard') }} </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-6 col-12 text-left text-md-right dashboard-filter mb-0">
            <form action="{{ route('dashboard.index',['test'=>'custom']) }}" method="get" class="d-inline-flex align-items-center flex-row-reverse" style="width: 100%; max-width: 250px; @media (max-width: 767px) { max-width: 100%; }">
                <button type="submit" class="btn btn-sm btn-primary float-right group-btn ml-0" style="height: 39px; margin-left: 0px">{{ __('levels.filter') }}</button>
                <input type="hidden" name="days" value="custom" />
                <input type="text" name="filter_date" placeholder="YYYY-MM-DD" autocomplete="off" class="form-control  date_range_picker float-right group-input" value="{{ $request->filter_date }}" required />
            </form>
        </div>
    </div>
    <!-- end pageheader  -->
    <div class="ecommerce-widget">
        <div class="row">
            <div class="col-12">
                <div class="header__summery">
                    @if(hasPermission('total_parcel') == true)
                    <div class="position-relative">
                        <a href="{{ route('parcel.index') }}">
                            <div class="card metric-card">
                                <div class="card-body">
                                    <div class="d-flex align-content-center gap-3">
                                        <label class="icon">
                                            <i class="fa fa-box-open"></i>
                                        </label>
                                        <div class="box-content w-100 text-left">
                                            <h5 class="title">{{ __('dashboard.total_parcel') }}</h5>
                                            <div class="count">
                                                {{ $data['total_parcel'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endif

                    @if(hasPermission('total_user') == true)
                    <div class="position-relative">
                        <a href="{{ route('users.index') }}">
                            <div class="card metric-card">
                                <div class="card-body">
                                    <div class="d-flex align-content-center gap-3">
                                        <label class="icon">
                                            <i class="fa fa-users"></i>
                                        </label>
                                        <div class="box-content w-100 text-left">
                                            <h5 class="title">{{ __('dashboard.total_user')}} </h5>
                                            <div class="count">
                                                {{ $data['total_user'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endif

                    @if(hasPermission('total_merchant') == true)
                    <div class="position-relative">
                        <a href="{{ route('customer.index') }}">
                            <div class="card metric-card">
                                <div class="card-body">
                                    <div class="d-flex align-content-center gap-3">
                                        <label class="icon">
                                            <i class="fa fa-users"></i>
                                        </label>
                                        <div class="box-content w-100 text-left">
                                            <h5 class="title">{{ __('dashboard.total_merchant')}} </h5>
                                            <div class="count">
                                                {{ $data['total_merchant'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endif

                    @if(hasPermission('total_delivery_man') == true)
                    <div class="position-relative">
                        <a href="{{ route('deliveryman.index') }}">
                            <div class="card metric-card">
                                <div class="card-body">
                                    <div class="d-flex align-content-center gap-3">
                                        <label class="icon">
                                            <i class="fas fa-users"></i>
                                        </label>
                                        <div class="box-content w-100 text-left">
                                            <h5 class="title">{{ __('dashboard.total_delivery_man')}} </h5>
                                            <div class="count">
                                                {{ $data['total_delivery_man'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endif

                    @if(hasPermission('total_accounts') == true)
                    <div class="position-relative">
                        <a href="{{ route('accounts.index') }}">
                            <div class="card metric-card">
                                <div class="card-body">
                                    <div class="d-flex align-content-center gap-3">
                                        <label class="icon">
                                            <i class="fa fa-credit-card"></i>
                                        </label>
                                        <div class="box-content w-100 text-left">
                                            <h5 class="title">{{ __('dashboard.total_accounts')}} </h5>
                                            <div class="count">
                                                {{ $data['total_accounts'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endif

                    @if(hasPermission('total_parcels_deliverd') == true)
                    <div class="position-relative">
                        <a href="{{ route('parcel.filter',['parcel_status'=>\App\Enums\ParcelStatus::DELIVERED]) }}">
                            <div class="card metric-card">
                                <div class="card-body">
                                    <div class="d-flex align-content-center gap-3">
                                        <label class="icon">
                                            <i class="fas fa-handshake"></i>
                                        </label>
                                        <div class="box-content w-100 text-left">
                                            <h5 class="title">{{ __('dashboard.total_deliverd')}} </h5>
                                            <div class="count">
                                                {{ $data['total_deliverd'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endif

                    @if(hasPermission('total_partial_deliverd') == true)
                    <div class="position-relative">
                        <a href="{{ route('parcel.filter',['parcel_status'=>\App\Enums\ParcelStatus::DELIVERY_FAILURE]) }}">
                            <div class="card metric-card">
                                <div class="card-body">
                                    <div class="d-flex align-content-center gap-3">
                                        <label class="icon">
                                            <i class="fa-solid fa-rectangle-xmark"></i>
                                        </label>
                                        <div class="box-content w-100 text-left">
                                            <h5 class="title">{{ __('dashboard.total_failure')}} </h5>
                                            <div class="count">
                                                {{ $data['total_failure'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <h5 class="mb-2">Marketplace Earnings</h5>
                <div class="header__summery">
                    <div class="position-relative">
                        <div class="card metric-card">
                            <div class="card-body">
                                <div class="d-flex align-content-center gap-3">
                                    <label class="icon">
                                        <i class="fas fa-check-circle"></i>
                                    </label>
                                    <div class="box-content w-100 text-left">
                                        <h5 class="title">Delivered Parcels</h5>
                                        <div class="count">
                                            {{ $data['marketplace_delivered'] ?? 0 }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="position-relative">
                        <div class="card metric-card">
                            <div class="card-body">
                                <div class="d-flex align-content-center gap-3">
                                    <label class="icon">
                                        <i class="fas fa-money-bill"></i>
                                    </label>
                                    <div class="box-content w-100 text-left">
                                        <h5 class="title">Delivery Revenue</h5>
                                        <div class="count">
                                            {{ settings()->currency }} {{ number_format((float) ($data['marketplace_delivery_charge'] ?? 0), 2) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="position-relative">
                        <div class="card metric-card">
                            <div class="card-body">
                                <div class="d-flex align-content-center gap-3">
                                    <label class="icon">
                                        <i class="fas fa-percentage"></i>
                                    </label>
                                    <div class="box-content w-100 text-left">
                                        <h5 class="title">Commission Earned</h5>
                                        <div class="count">
                                            {{ settings()->currency }} {{ number_format((float) ($data['marketplace_commission'] ?? 0), 2) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="position-relative">
                        <div class="card metric-card">
                            <div class="card-body">
                                <div class="d-flex align-content-center gap-3">
                                    <label class="icon">
                                        <i class="fas fa-wallet"></i>
                                    </label>
                                    <div class="box-content w-100 text-left">
                                        <h5 class="title">Rider Earnings</h5>
                                        <div class="count">
                                            {{ settings()->currency }} {{ number_format((float) ($data['marketplace_rider_earnings'] ?? 0), 2) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="position-relative">
                        <div class="card metric-card">
                            <div class="card-body">
                                <div class="d-flex align-content-center gap-3">
                                    <label class="icon">
                                        <i class="fas fa-coins"></i>
                                    </label>
                                    <div class="box-content w-100 text-left">
                                        <h5 class="title">Platform Earnings</h5>
                                        <div class="count">
                                            {{ settings()->currency }} {{ number_format((float) ($data['marketplace_platform_earning'] ?? 0), 2) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- salary and account section --}}

        <div class="row">
            @if(hasPermission('income_expense_charts') == true)
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="apexcharts" id="apexincomeexpense"></div>
                    </div>
                    <div class="card-footer">
                        <p class="font-weight-bold" style="font-size: 18px;">
                            <span class="legend-text text-primary d-inline-block">Total Parcel: {{ $data['total_parcel'] }}</span>
                            <span class="text-success float-right">Total Delivered: {{ $data['total_deliverd'] }}</span>
                        </p>
                    </div>
                </div>
            </div>
            @endif
        </div>
        <!-- recent parcel  -->

        @if(hasPermission('calendar_read') == true)
        <div class="row mb-5">
            <div class=" col-12 ">
                <div class="card mb-0 mt-4">
                    <div class="card-body ">
                        <div style="overflow:hidden;">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div id="datetimepicker12"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
</div>
</div>
<!-- end wrapper  -->

@endsection

<!-- css  -->
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" type="text/css" href="{{ static_asset('backend/vendor/calender/main.css') }}" />
<!-- Tempus Dominus Styles -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/eonasdan-bootstrap-datetimepicker/4.17.49/css/bootstrap-datetimepicker.min.css" integrity="sha512-ipfmbgqfdejR27dWn02UftaAzUfxJ3HR4BDQYuITYSj6ZQfGT1NulP4BOery3w/dT2PYAe3bG5Zm/owm7MuFhA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    .notification .nav-link.nav-icons {
        margin-top: 0px !important;
    }

    .admin-panel.notification .nav-link.nav-icons .indicator {
        top: 15px !important;
    }
</style>
@endpush
<!-- js  -->
@push('scripts')
<script type="text/javascript" src="{{ static_asset('backend/js/charts/apexcharts.js') }}"></script>
@include('backend.dashboard-charts')
@include('backend.calender-js')
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="{{ static_asset('backend/js/date-range-picker/dashboard-date-range-picker-custom.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- datetime -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"
    crossorigin="anonymous"></script>
<script type="text/javascript">
    $('#datetimepicker12').datetimepicker({
        inline: true,
        sideBySide: true
    });
    $('#province_id, #dispather_id, #merchant_id').on('change', function() {
        $(this).closest('form').submit();
    });
</script>
@endpush