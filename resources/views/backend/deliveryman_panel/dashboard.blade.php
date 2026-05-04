<!-- wrapper  -->
@extends('backend.partials.master')
@section('title')
    {{ __('merchant.dashboard') }}
@endsection
@section('maincontent')
    <div class="container-fluid dashboard-content ">
        <!-- pageheader  -->
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="page-header">
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{url('/')}}" class="breadcrumb-link">{{ __('deliveryman.dashboard') }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ __('deliveryman.driver_dashboard') }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div> 
        <!-- end pageheader  -->
        <div class="card"> 
            <div class="card-body"> 
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-link @if(!$request->parcel_history) active  @endif" href="{{ route('dashboard.index') }}"   >{{ __('parcel.matched_parcels') }}</a>
                        <a class="nav-link @if($request->parcel_history) active  @endif"  href="{{ route('dashboard.index',['parcel_history'=>'history']) }}">{{ __('parcel.parcel_history') }}</a> 
                    </div>
                  </nav>
                  <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">
                        @yield('dashboard-tab-content')
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- end wrapper  -->
@endsection()
 