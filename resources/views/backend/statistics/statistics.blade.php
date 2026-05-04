@extends('backend.partials.master')
@section('title')
{{ __('merchant.statistics') }} {{ __('levels.list') }}
@endsection
@section('maincontent')
<!-- wrapper  -->
<div class="container-fluid  dashboard-content">
    <!-- pageheader -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="page-header">
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard.index')}}" class="breadcrumb-link">{{ __('levels.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0)" class="breadcrumb-link">{{ __('merchant.statistics') }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- end pageheader -->
    <div class="row">
        <!-- data table  -->
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('merchant.statistics') }}</h4>
                    <div class="text-right">
                        @yield('filter_form')
                    </div>
                </div>
                <div class="card-body">
                    <!-- <div class="row p-0 mb-3">
                        <div class="col-12 col-md-6">
                            <p class="h3 d-inline">{{ __('merchant.statistics') }}</p>
                        </div>
                        <div class="col-12 col-md-6 text-right">
                             @yield('filter_form')
                        </div>
                    </div>  -->
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <a href="{{ route('admin.statistics') }}" class="nav-link {{ (request()->is('admin/statistics*')) ? 'active' : '' }}">{{ __('merchant.title') }}</a>
                            <a href="{{ route('admin.deliveryman.statistics') }}" class="nav-link {{ (request()->is('admin/deliveryman-statistics*')) ? 'active' : '' }}">{{ __('deliveryman.title') }}</a>
                        </div>
                    </nav>
                    <div class="tab-content">
                        <div class="tab-pane fade show active">
                            @yield('statistics-content')
                        </div>
                    </div>
                </div>
            </div>


        </div>
        <!-- end data table  -->
    </div>
</div>
<!-- end wrapper  -->
@endsection


@push('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush
@push('scripts')
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="{{ static_asset('backend/js/date-range-picker/date-range-picker-custom.js') }}"></script>
@endpush