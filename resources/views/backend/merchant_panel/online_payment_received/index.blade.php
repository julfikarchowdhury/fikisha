@extends('backend.partials.master')
@section('title')
{{ __('menus.payments_received') }} {{ __('levels.list') }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-container">
    <!-- pageheader -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="page-header">
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard.index')}}" class="breadcrumb-link">{{ __('levels.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link">{{ __('menus.payments_received') }}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{ __('levels.list') }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- end pageheader -->
    <div class="row">
        <div class="col-md-12 col-md-offset-3  ">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('menus.payments_received') }} {{ __('levels.list') }}</h4>
                </div>
                <div class=" card-body panel panel-default credit-card-box">
                    <!-- <p class="h3">{{ __('menus.payments_received') }} {{ __('levels.list') }}</p> -->
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table   " style="width:100%">
                                <thead>
                                    <tr>
                                        <th>{{ __('levels.id') }}</th>
                                        <th>{{ __('levels.card_type') }}</th>
                                        <th>{{ __('levels.from_account') }}</th>
                                        <th>{{ __('levels.transaction_id') }}</th>
                                        <th>{{ __('levels.amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i=1; @endphp
                                    @foreach($Payments as $payment)
                                    <tr>
                                        <td>{{$i++}}</td>
                                        <td>{{ @__('PaymentType.'.$payment->payment_type) }}</td>
                                        <td>
                                            <div>
                                                @if(@$payment->account->gateway == 1)
                                                {{ @$payment->account->user->name }} ( Cash )
                                                @elseif (@$payment->account->gateway == 2)
                                                {{ @$payment->account->account_holder_name }} <br />
                                                {{ @$payment->account->account_no }} <br />
                                                {{ @$payment->account->branch_name }}
                                                @else
                                                @if (@$payment->account->gateway == 3)
                                                Bkash
                                                @elseif (@$payment->account->gateway == 4)
                                                Rocket
                                                @elseif (@$payment->account->gateway == 5)
                                                Nagad
                                                @endif
                                                {{ @$payment->account->mobile }} <br />
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
                        <span>{{ $Payments->links() }}</span>
                        <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
                            {!! __('Showing') !!}
                            <span class="font-medium">{{ $Payments->firstItem() }}</span>
                            {!! __('to') !!}
                            <span class="font-medium">{{ $Payments->lastItem() }}</span>
                            {!! __('of') !!}
                            <span class="font-medium">{{ $Payments->total() }}</span>
                            {!! __('results') !!}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection