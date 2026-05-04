@extends('backend.partials.master')
@section('title')
{{ __('merchant.title') }} {{ __('levels.view') }}
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
                            <li class="breadcrumb-item"><a href="{{route('dashboard.index')}}" class="breadcrumb-link">{{ __('merchant.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">{{ __('merchantmanage.title') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{route('customer.index') }}" class="breadcrumb-link">{{ __('merchant.title') }}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{ __('levels.view') }}</a></li>
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
                    <!-- <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('merchant.title') }} {{ __('levels.view') }}</h4> -->
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 col-lg-12 col-xl-3 ">
                            <div class="card card-fluid">
                                <div class="card-body text-center">
                                    <a href="#" class="user-avatar user-avatar-xl my-3">
                                        <img src="{{@$singleMerchant->user->image}}" alt="User Avatar" class="rounded-circle user-avatar-xl">
                                    </a>
                                    <h3 class="card-title mb-2 text-truncate">
                                        <a href="#">{{@$singleMerchant->user->name}}</a>
                                    </h3>
                                    <h6 class="card-subtitle text-muted mb-3"> {{ __('levels.email') }}: {{@$singleMerchant->user->email}}</h6>
                                    <h6 class="card-subtitle text-muted mb-3"> {{ __('levels.phone') }}: {{@$singleMerchant->user->mobile}}</h6>

                                </div>
                                <div class="list-group list-group-flush merchant-view">
                                    <a href="{{ route('customer.view',$singleMerchant->id) }}" class="list-group-item list-group-item-action {{ (request()->is('admin/customer/view/'.$singleMerchant->id)) ? 'active' : '' }}">{{ __('merchant.company_information') }}</a>

                                    @if(hasPermission('merchant_shop_read') == true )
                                    <a href="{{ route('merchant.shops.index',$singleMerchant->id) }}" class="list-group-item list-group-item-action {{ (request()->is('admin/sender/'.$singleMerchant->id.'/shops*','admin/merchant/shops*')) ? 'active' : '' }}">{{ __('merchant.shop') }}</a>
                                    @endif
                                    @if(hasPermission('merchant_payment_read') == true )
                                    <a href="{{ route('merchant.paymentaccount.index',$singleMerchant->id) }}" class="list-group-item list-group-item-action {{ (request()->is('admin/sender/'.$singleMerchant->id.'/payment*')) ? 'active' : '' }}">{{ __('merchant.payment_account') }}</a>
                                    @endif
                                    @if(hasPermission('invoice_read') == true )
                                    <a href="{{ route('merchant.invoice.index',$singleMerchant->id) }}" class="list-group-item list-group-item-action {{ (request()->is('admin/sender/'.$singleMerchant->id.'/invoice*')) ? 'active' : '' }}">{{ __('menus.invoice') }}</a>
                                    @endif
                                </div>

                            </div>
                            <div class="card card-fluid">
                                <div class="list-group list-group-flush">
                                    <li class="list-group-item profile-list-group-item">
                                        <span class="float-left font-weight-bold">{{__('parcel.title')}} </span>
                                        <span class="float-right">

                                            {{ $singleMerchant->parcels->count()}}

                                        </span>
                                    </li>
                                    <li class="list-group-item profile-list-group-item">
                                        <span class="float-left font-weight-bold"> {{ __('merchant.amount') }} </span>
                                        <span class="float-right">

                                            {{ settings()->currency }} {{ $singleMerchant->parcels->sum('cash_collection') }}

                                        </span>
                                    </li>
                                    <li class="list-group-item profile-list-group-item">
                                        <span class="float-left font-weight-bold">{{ __('merchant.payble_amount') }}</span>
                                        <span class="float-right">
                                            {{ settings()->currency }} {{ $singleMerchant->parcels->sum('current_payable') }}
                                        </span>
                                    </li>
                                </div>
                            </div>
                            <div class="card card-fluid">
                                <div class="list-group list-group-flush">
                                    <li class="list-group-item profile-list-group-item">
                                        <span class="float-left font-weight-bold">{{ __('levels.account_status') }}</span>
                                        <span class="float-right">
                                            <a href="{{ route('customer.account_status',$singleMerchant->id) }}">
                                                {!! $singleMerchant->user->my_status !!}
                                            </a>
                                        </span>
                                    </li>
                                    <li class="list-group-item profile-list-group-item">
                                        <span class="float-left font-weight-bold">{{ __('levels.verification_status') }}</span>
                                        <span class="float-right">
                                            <a href="{{ route('customer.verification_status',$singleMerchant->id) }}">
                                                {!! $singleMerchant->user->my_verification_status !!}
                                            </a>
                                        </span>
                                    </li>
                                    <li class="list-group-item profile-list-group-item">
                                        <span class="float-left font-weight-bold">{{ __('levels.document_status') }}</span>
                                        <span class="float-right">
                                            <a href="{{ route('customer.document_status',$singleMerchant->id) }}">
                                                {!! $singleMerchant->user->my_document_status !!}
                                            </a>
                                        </span>
                                    </li>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 col-lg-12 col-xl-9  ">
                            @yield('backend.merchant.layout.list')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection()