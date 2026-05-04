@extends('backend.partials.master')
@section('title')
   {{ __('levels.service') }} {{ __('levels.list') }}
@endsection
@section('maincontent')

<div class="container-fluid  dashboard-content">
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="page-header">
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">{{__('levels.front_web')}}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link">{{ __('levels.service') }}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{ __('levels.list') }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('levels.service') }}</h4>
                    @if(hasPermission('service_create'))
                    <a href="{{route('service.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="top" title="{{ __('levels.add') }}"><i class="fa fa-plus"></i></a>
                    @endif
                </div>
                <div class="card-body">

                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <a class="nav-link text-primary {{ request()->is('admin/front-web/service/outside*') ? '' : 'active' }}" href="{{ route('service.index') }}"  >Inside City</a>
                            <a class="nav-link text-primary {{ request()->is('admin/front-web/service/outside*') ? 'active' : '' }}" href="{{ route('service.outside') }}">Outside City</a>
                        </div>
                    </nav>
                   
                    @yield('service-table')
 
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

