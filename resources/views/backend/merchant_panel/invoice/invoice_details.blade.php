@extends('backend.partials.master')
@section('title')
{{ __('menus.invoice') }} {{ __('levels.details') }}
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
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">{{ __('menus.invoice') }}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{ @$invoice->invoice_id }}</a></li>
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
                <div class="card-body">
                    <div class="row  ">
                        <div class="col-12 ">
                            <div class="d-flex justify-content-end mt-2 mt-md-0"> 
                                <a href="{{ route('merchant.panel.invoice.pdf',[$invoice->merchant_id,$invoice->invoice_id]) }}" class="btn btn-secondary btn-sm mr-2" data-toggle="tooltip" data-placement="top" title="Add"><i class="fa fa-download"></i> PDF</a>
                                <a href="{{ route('merchant.panel.invoice.csv',[$invoice->merchant_id,$invoice->invoice_id]) }}" class="btn btn-success btn-sm " data-toggle="tooltip" data-placement="top" title="Add"><i class="fa fa-download"></i> CSV</a>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">

                        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                            <tbody>
                              
                                <tr>
                                    <td  style="border-bottom: none!important;padding:0px">
                                        <table >
                                            <tr>
                                                <td style="padding:0px;border-bottom:none!important">
        
                                                    <div style="padding:10px;line-height:1.5;">
                                                        <span><i class="fa-sharp fa-solid fa-file-invoice" style="font-size: 15px"></i> Invoice to <br/>
                                                        <b>{{ $invoice->merchant->business_name }}</b>  </span><br>
                                                        <span> {{$invoice->merchant->user->mobile}} </span><br>
                                                        <span> {{$invoice->merchant->address}}</span><br>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="border-bottom:none!important;padding:0px;width:40%">
 
                                        <table width="100%" >
                                            <tr>
                                                <td style="padding:0px">
                                                    <table style="float: right">
                                                        <tr>
                                                            <td style="padding:5px!important;">
                                                                <div class="d-inline-block"> 
                                                                    <b>Invoice Date: </b>{{ $invoice->invoice_date }}<br/>
                                                                    <b>Invoice:</b> {{ $invoice->invoice_id }}<br/>
                                                                    <b>Total paid out: </b>{{ number_format($invoice->invoiceParcels->sum('current_payable'),2) }}
                                                                </div>
                                                            </td>
                                                        </tr> 
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <table class="table   " style="width:100%">
                            <thead>
                                <tr>
                                     
                                    <th>{{  __('levels.id') }}</th>
                                    <th>{{ __('menus.date') }}</th>
                                    <th>{{ __('invoice.invoice') }}</th>
                                    <th>{{ __('levels.track_id') }}</th>
                                    <th>{{ __('parcel.customer_info')}}</th>
                                    <th>{{ __('parcel.delivery_charge')}}</th>
                                    <th>{{ __('parcel.vat')}}</th>
                                    <th>{{ __('parcel.discount')}}</th> 
                                    <th>{{ __('parcel.Total_Charge')}}</th>
                                    <th>{{ __('invoice.paid_out')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i=0;
                                @endphp
                                 @foreach ($invoiceParcels as $parcel)
                                    <tr>
                                        <td>{{++$i}}</td>
                                        <td>{{\Carbon\Carbon::parse($parcel->updated_at)->format('d-m-Y')}}</td>
                                        <td>{{@$parcel->parcel->invoice_no}}</td>
                                        <td>{{@$parcel->parcel->tracking_id}}</td>
                                        <td>
                                            <p style="margin-bottom: 0px!important">
                                                {{ @$parcel->parcel->customer_first_name . ' ' . @$parcel->parcel->customer_last_name }}
                                            </p>
                                            <p style="margin-bottom: 0px!important">
                                                {{ @$parcel->parcel->customer_company_name }}
                                            </p> 
                                            <p style="margin-bottom: 0px!important">{{ @$parcel->parcel->customer_phone }}</p> 
                                        </td> 
                                        <td>{{ settings()->currency }}{{@$parcel->total_delivery_amount}}</td>
                                        <td>{{ settings()->currency }}{{@$parcel->vat_amount}}</td>
                                        <td>{{ settings()->currency }}{{@$parcel->discount_amount}}</td>
                                        <td>{{ settings()->currency }}{{@$parcel->total_shipping_fee}}</td>
                                        <td>{{ settings()->currency }}{{@$parcel->current_payable}}</td>
                                    </tr>
                                @endforeach
                                <tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-12">
                    <div class="pb-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <span>{{ $invoiceParcels->links() }}</span>
                        <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
                            {!! __('Showing') !!}
                            <span class="font-medium">{{ $invoiceParcels->firstItem() }}</span>
                            {!! __('to') !!}
                            <span class="font-medium">{{ $invoiceParcels->lastItem() }}</span>
                            {!! __('of') !!}
                            <span class="font-medium">{{ $invoiceParcels->total() }}</span>
                            {!! __('results') !!}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <!-- end table  -->
    </div>
</div>
<!-- end wrapper  -->
@endsection()
