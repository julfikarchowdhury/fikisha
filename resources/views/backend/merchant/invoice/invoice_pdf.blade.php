<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge"> 
    <style>
        body{
            margin: 0px;
            padding:0px;
        }
        *{
            font-size: 12px;
        }
        table th{
            padding:10px;
            text-align: left;
        }
        table td{
            padding:10px;
            text-align: left;
            border-bottom:1px solid rgba(73, 73, 73, 0.226);
        }
        .logo-img{
            width: 100px!important;
            object-fit: contain!important;
        }
    </style>
</head>
<body>
    <table width="100%">
        <tr>
            <td style="text-align: center;border-bottom:none!important;padding:0px;padding-bottom:5px">
                 
                <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">
                    <tbody> 
                        <tr> 
                            <td  style="border-bottom: none!important;padding:0px;width:15%">
                                <div style="text-align: left;margin-bottom:10px!important">
                                    @php
                                         if(file_exists(public_path(settings()->rxlogo->original))):
                                            $logo = public_path(settings()->rxlogo->original);
                                        else:
                                            $logo =  public_path('images/default/logo.png');
                                        endif;  
                                    @endphp     
                                    <img class="logo-img" src="{{ $logo }}"  style="height: 40px;object-fit:contain!important" class="logo" alt="logo">
                                </div>
                            </td>
                            <td  style="border-bottom: none!important;padding:0px">
                                <table>
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
                                        <td style="border:1px solid rgba(73, 73, 73, 0.226)!important;padding:0px">
                                            <table width="100%">
                                                <tr>
                                                    <td style="border-right:1px solid rgba(73, 73, 73, 0.226);padding:5px!important;"><b>Invoice Date </b></td>
                                                    <td style="padding:5px!important;">{{ $invoice->invoice_date }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="border-right:1px solid rgba(73, 73, 73, 0.226);padding:5px!important"><b>Invoice #</b></td>
                                                    <td style="padding:5px!important;">{{ $invoice->invoice_id }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="border-bottom:none!important;border-right:1px solid rgba(73, 73, 73, 0.226);padding:5px!important;"><b>Total paid out</b></td>
                                                    <td style="border-bottom:none!important;padding:5px!important;">{{ number_format($invoice->invoiceParcels->sum('current_payable'),2) }} </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
    <table   cellspacing="0" width="100%">
        <tr style="background-color: #b21419;color:white">
         
            <th>{{ __('menus.date') }}</th>
            <th>{{ __('invoice.invoice') }}</th>
            <th>{{ __('levels.track_id') }}</th>
            <th>{{ __('parcel.customer_info')}}</th>
            <th>{{ __('parcel.status')}}</th>
            <th>{{ __('parcel.delivery_charge')}}</th>
            <th>{{ __('parcel.vat')}}</th>
            <th>{{ __('parcel.discount')}}</th> 
            <th>{{ __('parcel.Total_Charge')}}</th>
            <th>{{ __('invoice.paid_out')}}</th>

        </tr>
        @foreach ($invoice->invoiceParcels as $parcel)
            
            <tr>
                <td style="padding: 2px 10px;">{{\Carbon\Carbon::parse($parcel->parcel->updated_at)->format('d-m-Y')}}</td>
                <td style="padding: 2px 10px;"  >{{@$parcel->parcel->invoice_no}}</td>
                <td style="padding: 2px 10px;">{{@$parcel->parcel->tracking_id}}</td>
                <td style="padding: 2px 10px;">
                    {{ @$parcel->parcel->customer_first_name . ' ' . @$parcel->parcel->customer_last_name }}<br/>
                    {{ @$parcel->parcel->customer_company_name }} 
                    {{ @$parcel->parcel->customer_phone }} 
                </td>
                <td style="padding: 2px 10px;">
                   {{ __('parcelStatus.'.$parcel->parcel_status) }}
                </td>

                <td>{{ settings()->currency }}{{@$parcel->total_delivery_amount}}</td>
                <td style="background-color: rgb(73 73 73 / 7%); padding: 2px 10px;">{{ settings()->currency }}{{@$parcel->vat_amount}}</td>
                <td style="background-color: rgb(73 73 73 / 7%); padding: 2px 10px;">{{ settings()->currency }}{{@$parcel->discount_amount}}</td>
                <td style="background-color: rgb(73 73 73 / 7%); padding: 2px 10px;">{{ settings()->currency }}{{@$parcel->total_shipping_fee}}</td>
                <td style="padding: 2px 10px;">{{ settings()->currency }}{{@$parcel->current_payable}}</td> 
            </tr>
            @endforeach
            <tr style="background-color: #b21419;color:white">
                <th style="text-transform: uppercase; padding: 2px 10px;" colspan="8">Total</th> 
                <th style="padding: 2px 10px;"> {{ number_format($invoice->invoiceParcels->sum('total_shipping_fee'),2) }}  </th>
                <th style="padding: 2px 10px;"> {{ number_format($invoice->invoiceParcels->sum('current_payable'),2) }}  </th>
            </tr>
    </table>
    <table style="width:100%;border:none!important;padding:0px">
        <tr>
            <td style="border-bottom:none!important">
                <div>
                    <span><b>Terms and Conditions</b></span><br/>
                    <p>
                        Payment should be made within 48 hours by bank or mobile-banking.
                    </p>
                </div>
            </td>
            <td style="border-bottom:none!important">
                <div >
                    <p style="text-align: left;width:150px;float: right;">
                        (This is a computer
                        generated invoice
                        and requires no
                        signature)
                    </p>
                </div>
            </td>
        </tr>

    </table>

</body>
</html>
