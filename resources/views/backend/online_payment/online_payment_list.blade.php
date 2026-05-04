
@extends('backend.partials.master')
@section('title')
    {{ __('menus.payments') }}    {{ __('levels.list') }}
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
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link">{{ __('menus.payments') }}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{ __('levels.list') }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- table  -->
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('menus.payments') }}</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table   " style="width:100%">
                            <thead>
                            <tr>
                                <th>{{ __('levels.id') }}</th>
                                <th>{{ __('levels.card_type') }}</th>
                                <th>{{ __('merchant.title') }}</th>
                                <th>{{ __('levels.to_account') }}</th>
                                <th>{{ __('levels.transaction_id') }}</th>
                                <th>{{ __('levels.amount') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $i=1; @endphp
                            @foreach($payments as $payment)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>{{ @__('PaymentType.'.$payment->payment_type) }}</td>
                                    <td>
                                        <div class="row">
                                            <div class="pr-3">
                                                <img src="{{@$payment->merchant->user->image}}" alt="user" class="rounded" width="40" height="40">
                                            </div>
                                            <div>
                                                <strong>{{$payment->merchant->business_name}}</strong>
                                                <p>{{@$payment->merchant->user->email}}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            @if(@$payment->account->gateway == 1)
                                                {{@$payment->account->user->name }} (Cash)
                                            @elseif (@$payment->account->gateway == 2)
                                                {{ @$payment->account->account_holder_name }} <br/>
                                                {{ @$payment->account->account_no }} <br/>
                                                {{ @$payment->account->branch_name }}
                                            @else
                                                @if (@$payment->account->gateway == 3)
                                                    Bkash
                                                @elseif (@$payment->account->gateway == 4)
                                                    Rocket
                                                @elseif (@$payment->account->gateway == 5)
                                                    Nagad
                                                @endif
                                                {{ @$payment->account->mobile }} <br/>
                                                {{ @$payment->account->account_type }}
                                            @endif
                                        </div>
                                    </td>
                                    <td> {{ @$payment->transaction_id }} </td>
                                    <td> {{ @$payment->amount }} </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="pb-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span>{{ $payments->links() }}</span>
                    <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
                        {!! __('Showing') !!}
                        <span class="font-medium">{{ $payments->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-medium">{{ $payments->lastItem() }}</span>
                        {!! __('of') !!}
                        <span class="font-medium">{{ $payments->total() }}</span>
                        {!! __('results') !!}
                    </p>
                </div>
            </div>
        </div>
        <!-- end table  -->
    </div>
</div>
<!-- end wrapper  -->
@endsection()
