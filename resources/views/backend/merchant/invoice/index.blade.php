@extends('backend.partials.master')
@section('title')
{{ __('menus.invoice') }} {{ __('levels.list') }}
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
                            <li class="breadcrumb-item"><a href="{{ route('customer.index') }}" class="breadcrumb-link">{{ __('menus.merchants') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('customer.view',$merchant_id) }}" class="breadcrumb-link">{{ __('levels.view') }}</a></li>
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">{{ __('invoice.invoice') }}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{ __('levels.list') }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- end pageheader -->
    <div class="row">
        <!-- table  -->
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('menus.invoice') }} {{ __('levels.list') }}</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table   " style="width:100%">
                            <thead>
                                <tr>
                                    <th>{{ __('levels.id') }}</th>
                                    <th>{{ __('menus.invoice') .' '. __('invoice.id')}}</th>
                                    <th>{{ __('menus.invoice') . ' '. __('levels.date')}}</th>
                                    <th>{{ __('parcel.Total_Charge')}}</th>
                                    <th>{{ __('parcel.current_payable')}}</th>
                                    <th>{{ __('parcel.status')}}</th>
                                    @if(hasPermission('invoice_status_update') == true)
                                    <th>{{ __('invoice.status_update')}}</th>
                                    @endif
                                    <th>{{ __('levels.actions')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $i=0;
                                @endphp
                                @foreach ($invoices as $invoice)
                                <tr>
                                    <td>{{++$i}}</td>
                                    <td>{{@$invoice->invoice_id}}</td>
                                    <td>{{@$invoice->invoice_date}}</td>
                                    <td>{{ settings()->currency }}{{@$invoice->total_charge}}</td>
                                    <td>{{ settings()->currency }}{{@$invoice->current_payable}}</td>
                                    <td>{!! $invoice->my_status !!}</td>
                                    @if(hasPermission('invoice_status_update') == true)
                                    <td>
                                        @if ($invoice->status == \App\Enums\InvoiceStatus::PAID)
                                        ...
                                        @else
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend be-addon">
                                                <button tabindex="-1" data-toggle="dropdown" type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split">
                                                    <i class="fa fa-cogs"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    {!! $invoice->update_status !!}
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </td>
                                    @endif
                                    <td>
                                        <a href="{{ route('merchant.invoice.details',[$merchant_id,$invoice->invoice_id]) }}" class="btn btn-sm btn-primary mt-1"><i class="fa fa-eye"></i> View</a>
                                        <a href="{{ route('merchant.invoice.pdf',[$merchant_id,$invoice->invoice_id]) }}" class="btn btn-sm btn-secondary mt-1"> <i class="fa fa-download"></i> PDF</a>
                                        <a href="{{ route('merchant.invoice.csv',[$merchant_id,$invoice->invoice_id]) }}" class="btn btn-sm btn-success mt-1"><i class="fa fa-download"></i> CSV</a>
                                    </td>
                                </tr>
                                @endforeach
                                <tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="pb-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span>{{ $invoices->links() }}</span>
                    <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
                        {!! __('Showing') !!}
                        <span class="font-medium">{{ $invoices->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-medium">{{ $invoices->lastItem() }}</span>
                        {!! __('of') !!}
                        <span class="font-medium">{{ $invoices->total() }}</span>
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