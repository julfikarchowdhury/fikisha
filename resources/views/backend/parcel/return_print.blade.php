<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Print</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        body {
            font-family:Arial, Helvetica, sans-serif;
            background: #e7e9ed;
            font-size: 14px;
            line-height: 18px;
            color: black;
        }

        .main {
            margin: 15px auto;
            padding: 20px;
            width: 100mm;
            height: 150mm;
            background-color: #fff;
            border: 1px solid #ccc;
            -moz-border-radius: 6px;
            -webkit-border-radius: 6px;
            -o-border-radius: 6px;
            border-radius: 6px;
        }

        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
            margin-top: 5px;
            margin-bottom: 5px;
        }
        
        p {
           line-height: 20px;
        }
        
        h1, h2, h3, h4{
           line-height: 20px;
        }

        td,
        th {
            border: 0.5px solid black;
            text-align: left;
            padding: 2px;
        }

        .header {
            width: 100%;
            overflow: hidden;
        }

        .header-left {
            float: left;
            width: 50%;
            text-align: left;
            overflow: hidden;
        }

        .header-right {
            float: left;
            width: 50%;
            text-align: right;
            overflow: hidden;
        }

        @media print {
            @page {
                size: 100mm 150mm !important;
                margin: 2mm;
            }

            body {
                background: #fff;
                color: black;
            }

            .main {
                margin: 0;
                padding: 0;
                width: 100mm !important;
                height: 150mm !important;
                border: none;
            }

            table {
                font-family: arial, sans-serif;
                border-collapse: collapse;
                width: 100%;
                margin-top: 5px;
                margin-bottom: 5px;
            }

            td,
            th {
                border: 0.5px solid black !important;
                text-align: left;
                padding: 2px;
            }
        }
    </style>
</head>

<body>
    <div class="main">
        <div class="header">
            <div class="header-left">
                <img src="{{ settings()->logo_image }}" style="width: 70%; height: 60px;">
                @if ($parcel->from_state_id == $parcel->to_state_id)
                    <h1>Inside</h2>
                @else
                    <h1>Outside</h2>
                @endif
                <h4>{{ @$parcel->fromProvince->name }}/{{ @$parcel->toProvince->name }}</h4>
                <p>{{ @$parcel->ShippingType->title }}</p>
                <p></p>
                <div>
                    <h1 style="font-weight: bold; margin-bottom: 10px; margin-top: 10px; text-align: center;">Returned</h1>
                    <p style="font-weight: bold;">Return Charge: {{ settings()->currency }}{{ $parcel->return_charges }}</p>
                </div>
            </div>
            <div class="header-right">
                <p style="font-size: 60px;font-weight: bold;line-height: 45px;">
                    {{ @$parcel->fromProvince->province_code }}
                </p>
                @if ($parcel->return_date_time)
                    <p>Return date/time: {{ \Carbon\Carbon::parse($parcel->return_date_time)->format('d M Y, H:i a') }}</p>
                @else
                    <p>Return date/time: {{ $parcel->updated_at->format('d M Y, H:i a') }}</p>
                @endif
                <p>Tracking ID: {{ $parcel->tracking_id }}</p>
                <img src="{!! $parcel->qrcodeprint !!}" style="height: 80px;margin-top: 10px;">
            </div>
        </div>
        <div class="main-content">
            <table class="table">
                <tbody>
                    <tr>
                        <td>
                            @if(@$parcel->cbm_details['package_type_id'] == 1)
                                {{ __('parcel.courier_document') }}
                            @else
                                {{ __('parcel.parcel_type') }}
                            @endif
                            <br>
                            <b>Items Qte:</b>{{ number_format(@$parcel->cbm_details['quantity'], 2) }}
                        </td>
                        <td>
                            <b>Content:</b> {{ @$parcel->cbm_details['content_parcel'] }}
                            @if (@$parcel->cbm_details['rush_hour_service'])
                            <br>
                            <b>Priority:</b>{{ 'Urgent' }}
                            @elseif (@$parcel->pick_type == 1)
                            <br>
                            <b>Priority:</b>{{ 'Same Day' }}
                            @endif
                        </td>
                        <td>
                            <b>Total Weight in Kg:</b> {{ number_format(@$parcel->cbm_details['local_weight'], 2) }}
                            kg
                        </td>
                        <td>
                            <b>TV here in M3:</b> {{ number_format(@$parcel->cbm_details['total_cbm'], 3) }}
                            M<sup>3</sup>
                        </td>
                    </tr>
                    @foreach ($parcel->items as $key => $item)
                    <tr>
                        <td>
                            @if(@$item->package_type_id == 1)
                                {{ __('parcel.courier_document') }}
                            @else
                                {{ __('parcel.parcel_type') }}
                            @endif
                            <br>
                            <b>Items Qte:</b>{{ number_format(@$item->quantity, 2) }}
                        </td>
                        <td>
                            Content: {{ @$item->content_parcel }}
                            @if (@$item->rush_hour_service)
                            <br>
                            Priority:{{ 'Urgent' }}
                            @elseif ($parcel->pick_type == 1)
                            <br>
                            Priority:{{ 'Same Day' }}
                            @endif
                        </td>
                        <td>
                            <b>Total Weight in Kg:</b> {{ number_format(@$item->weight, 2) }} kg
                        </td>
                        <td>
                            <b>TV here in M3:</b> {{ number_format(@$item->total_cbm, 3) }} M<sup>3</sup>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td>
                            <b>Parcels:</b>{{ @$parcel->items->sum('quantity') + $parcel->cbm_details['quantity'] }}
                        </td>
                        <td></td>
                        <td>
                            <b>Total Weight:</b>{{ @$parcel->items->sum('weight')+@$parcel->cbm_details['total_weight'] }} kg<br>
                        </td>
                        <td>
                            <b>Total TV here in M3:</b>{{ number_format((@$parcel->items->sum('total_cbm') + @$parcel->cbm_details['total_cbm']),3) }} M<sup>3</sup>
                        </td>
                    </tr>
                </tfoot>
            </table>
            <div class="information">
                <p style="font-weight: bold;">TC: {{ settings()->currency }}{{ $parcel->current_payable }}</p>
                <p>
                    Sender:
                    {{ trim(($parcel->sender_first_name ?? '') . ' ' . ($parcel->sender_last_name ?? '')) }}
                    @if ($parcel->pickup_address)
                        ,{{ $parcel->pickup_address }}
                    @endif
                    @if ($parcel->sender_phone)
                        ,{{ $parcel->sender_phone }}
                    @endif
                </p>
                <p>
                    Recipient:
                    {{ trim(($parcel->customer_first_name ?? '') . ' ' . ($parcel->customer_last_name ?? '')) }}
                    @if ($parcel->customer_address)
                        ,{{ $parcel->customer_address }}
                    @endif
                    @if ($parcel->customer_phone)
                        ,{{ $parcel->customer_phone }}
                    @endif
                </p>
                @php($text_data = '')
                @if((int)$parcel->liquid_fragile_amount > 0)
                    @php($text_data .= 'F ')
                @endif
                @if((int)$parcel->rush_hour_amount > 0)
                    @php($text_data .= 'U ')
                @endif
                @if((int)$parcel->scheduled_amount > 0)
                    @php($text_data .= 'S ')
                @endif
                <p style="font-size: 30px; text-transform: uppercase;font-weight: bold;margin-top: 3px;">{{ $text_data ? $text_data : '---' }}</p>
            </div>
            <table class="table">
                <tr>
                    <td style="vertical-align: middle;text-align: center;">
                        <span class="bc_center" id="pr-bcTarget">{!! $parcel->barcodeprint !!}</span> 
                        <span style="font-weight: bold;font-size:15px">{{ $parcel->tracking_id }}</span>
                    </td>
                </tr>
            </table>
        </div>
        <footer class="footer" style="margin-top: 10px;">
            <div style="text-align: center !important">
                <strong style="font-size: 20px;">{{ settings()->name }}</strong>
                <p style="font-size: 16px;">{{ settings()->phone }}, {{ config('app.site_domain') }}</p>
            </div>
        </footer>
    </div>
    <script>
        // window.print();
    </script>
</body>
</html>