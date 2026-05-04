<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title> {{ __('reports.parcel_reports') }} | print</title>
    <link rel="shortcut icon" href="{{ static_asset(settings()->favicon_image)}}" type="image/x-icon">
    <link rel="stylesheet" href="{{ static_asset('backend/')}}/css/reports_print.css">
</head>

<body>
    <div class="print" style="text-align: right">
        <a href="#" class="btn-danger" id="close" onclick="window.close()">{{ __('menus.cancel') }}</a>
    </div>
    <div>
        <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="officehead">
            <tbody>
                <tr>
                    <td class="left-col" style="height: 70px;  border-right: 3px solid">
                        <img alt="Logo" src="{{ settings()->logo_image }}" class="logo" style="max-height: 70px;">
                    </td>
                    <td style="padding-left: 10px;line-height: 1.2;" class="right-col">
                        <span> <b style="letter-spacing: 3px;"></b> {{ settings()->name }}</span><br>
                        <span>{{ settings()->email }}</span><br>
                        <span style="  padding-top: 3px; "> {{ settings()->phone }} </span><br>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="invoice" id="printablediv">
                <div class="row mt-3" style="width:100%">
                    <div class="col-sm-12  ">
                        <h3 style="text-align: center"> {{ __('reports.parcel_reports') }}</h3>
                    </div>
                </div>
                <div class="row mt-3" style="width:100%">
                    <div class="col-sm-12 " style="overflow: hidden">
                        <span style="float: left"></span>
                        <span style="float: right">
                            <font style="font-weight: bold">{{ __('menus.date') }} :</font> {{ dateFormat(date('Y-m-d')) }}
                        </span>
                    </div>
                </div>
                <hr>
                <div class="row" style="margin-top: 20px">
                    <div class="col-sm-12 table-responsive">
                        <table class="table" style="width:100%">
                            <thead>
                                <tr style="background-color: var(--bs-primary);color:white">
                                    <th style=" padding: 5px 10px;">#</th>
                                    <th style=" padding: 5px 10px;">Invoice ID</th>
                                    <th style=" padding: 5px 10px;">Tracking ID</th>
                                    <th style=" padding: 5px 10px;">Customer Info</th>
                                    <th style=" padding: 5px 10px;">Status</th>
                                    <th style=" padding: 5px 10px;">Base Charge</th>
                                    <th style=" padding: 5px 10px;">Receiver Markup</th>
                                    <th style=" padding: 5px 10px;">Final Paid</th>
                                    <th style=" padding: 5px 10px;">Vat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($parcels) > 0)
                                    @foreach ($parcels as $key=> $parcel)
                                        <tr>
                                            <td style=" padding: 5px 10px;">{{ ++$key }}</td>
                                            <td style=" padding: 5px 10px;">{{ @$parcel->invoice_no }}</td>
                                            <td style=" padding: 5px 10px;">{{ @$parcel->tracking_id }}</td>
                                            <td style=" padding: 5px 10px;"  >{{ @$parcel->customer_name }}<br/>{{ @$parcel->customer_phone }}<br/>{{ @$parcel->customer_address }}</td>
                                            <td style=" padding: 5px 10px;"  >{!! @$parcel->parcel_status !!}</td>
                                            <td style="background-color: rgb(73 73 73 / 7%); padding: 5px 10px;">{{ @$parcel->base_delivery_charge ?? 0 }}</td>
                                            <td style="background-color: rgb(73 73 73 / 7%); padding: 5px 10px;">{{ @$parcel->receiver_markup ?? 0 }}</td>
                                            <td style=" padding: 5px 10px;">{{ @$parcel->final_paid_amount ?? 0 }}</td>
                                            <td style="background-color: rgb(73 73 73 / 7%); padding: 5px 10px;">{{ @$parcel->vat_amount ?? 0 }}</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td style=" padding: 5px 10px; text-align: center;" colspan="9">No records available</td>
                                    </tr>
                                @endif
                            </tbody>
                            <tfoot>
                                <tr style="background-color: var(--bs-primary);color:white">
                                    <th style="text-transform: uppercase; padding: 5px 10px;" colspan="5">Total</th>
                                    <th style=" padding: 5px 10px;"> {{ number_format($parcels->sum('base_delivery_charge'),2) }} </th>
                                    <th style=" padding: 5px 10px;"> {{ number_format($parcels->sum('receiver_markup'),2) }} </th>
                                    <th style=" padding: 5px 10px;"> {{ number_format($parcels->sum('final_paid_amount'),2) }}  </th>
                                    <th style=" padding: 5px 10px;"> {{ number_format($parcels->sum('vat_amount'),2) }}  </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        window.print();
    </script>

</body>

</html>