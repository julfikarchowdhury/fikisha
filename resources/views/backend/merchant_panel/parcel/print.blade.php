<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print</title>
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
            /* height: 150mm; */
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

        .head-logo{
            text-align: center
        }
    </style>
</head>

<body>
    <div class="main">
        <div class="head-logo">
            <img src="{{ settings()->logo_image }}" style="width: 70%; height: 60px;object-fit:contain">
            @if ($parcel->from_state_id == $parcel->to_state_id)
                <h1>Inside</h2>
            @else
                <h1>Outside</h2>
            @endif
            <h4>{{ @$parcel->fromProvince->name }}/{{ @$parcel->toProvince->name }}</h4>
            <p>{{ @$parcel->ShippingType->title }}</p>
            <p>
                @php
                    $from_point = $parcel->from_point_type;
                    $to_point = $parcel->to_point_type;
                    if ($from_point == 1 && $to_point == 1) {
                        //door_to_door
                        echo "";
                    } else if ($from_point == 1 && $to_point == 2) {
                        //door_to_hub
                        echo @$parcel->toHub->name.' ('.@$parcel->toHub->id.')';
                    } else if ($from_point == 2 && $to_point == 1) {
                        //hub_to_door
                        echo @$parcel->fromHub->name.' ('.@$parcel->fromHub->id.')';
                    } else if ($from_point == 2 && $to_point == 2) {
                        //hub_to_hub
                        echo @$parcel->fromHub->name.' ('.@$parcel->fromHub->id.')/'.@$parcel->fromHub->name.'('.@$parcel->fromHub->id.')';
                    }
                @endphp
            </p>
        </div>
        <div class="header">
            <div class="header-left">
                <p style="font-size: 60px;font-weight: bold;line-height: 45px;">
                    {{ @$parcel->toProvince->province_code }}
                </p>
                <p>Order date/time: {{ $parcel->created_at->format('d M Y, H:i a') }}</p>
                <p>Tracking ID: {{ $parcel->tracking_id }}</p>
            </div>
            <div class="header-right">
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
                @if(@$parcel->merchant && @$parcel->to_merchant)
                    @if(@$parcel->merchant->account_type == 2 && @$parcel->to_merchant->account_type == 2)
                    <p>Sender:
                        @if (@$parcel->merchant->user->name)
                            {{ @$parcel->merchant->user->name }}
                        @endif
                        @if (@$parcel->merchant->user->address)
                            ,{{ @$parcel->merchant->user->address }} 
                        @endif
                        @if (@$parcel->merchant->user->mobile)
                            ,{{ @$parcel->merchant->user->mobile }}
                        @endif
                        @if (@$parcel->merchant->user->portal_code)
                            ,{{ @$parcel->merchant->user->portal_code }}
                        @endif
                        @if (@$parcel->merchant->user->province->name)
                            {{ @$parcel->merchant->user->province->name }}
                        @endif
                    </p>
                    <p>Recipient:
                        @if (@$parcel->to_merchant->user->name)
                            {{ @$parcel->to_merchant->user->name }}
                        @endif
                        @if (@$parcel->to_merchant->user->address)
                            ,{{ @$parcel->to_merchant->user->address }}
                        @endif
                        @if (@$parcel->to_merchant->user->mobile)
                            ,{{ @$parcel->to_merchant->user->mobile }}
                        @endif
                        @if (@$parcel->to_merchant->user->portal_code)
                            ,{{ @$parcel->to_merchant->user->portal_code }}
                        @endif
                        @if (@$parcel->to_merchant->user->province->name)
                            {{ @$parcel->to_merchant->user->province->name }}
                        @endif
                    </p>
                    @endif
                @endif
                @if(@$parcel->merchant && @$parcel->customer)
                    @if(@$parcel->merchant->account_type == 2 && @$parcel->to_account_type == 1)
                    <p>Sender:
                        @if (@$parcel->merchant->user->name)
                            {{ @$parcel->merchant->user->name }}
                        @endif
                        @if (@$parcel->merchant->user->address)
                            ,{{ @$parcel->merchant->user->address }}
                        @endif
                        @if (@$parcel->merchant->user->mobile)
                            ,{{ @$parcel->merchant->user->mobile }}
                        @endif
                        @if (@$parcel->merchant->user->portal_code)
                            ,{{ @$parcel->merchant->user->portal_code }}
                        @endif
                        @if (@$parcel->merchant->user->province->name)
                            {{ @$parcel->merchant->user->province->name }}
                        @endif
                    </p>
                    <p>Recipient:
                        @if (@$parcel->customer->first_name)
                            {{ @$parcel->customer->first_name.' '.@$parcel->customer->last_name }}
                        @endif
                        @if (@$parcel->customer->address)
                            ,{{ @$parcel->customer->address }}
                        @endif
                        @if (@$parcel->customer->phone_number)
                            ,{{ @$parcel->customer->phone_number }}
                        @endif
                        @if (@$parcel->customer->portal_code)
                            ,{{ @$parcel->customer->portal_code }}
                        @endif
                        @if (@$parcel->customer->province->name)
                            {{ @$parcel->customer->province->name }}
                        @endif 
                    </p>
                    @endif
                @endif
                @if(@$parcel->merchant && @$parcel->to_merchant)
                    @if(@$parcel->merchant->account_type == 1 && @$parcel->to_merchant->account_type == 2)
                    <p>Sender:
                        @if (@$parcel->merchant->user->name)
                            {{ @$parcel->merchant->user->name }}
                        @endif
                        @if (@$parcel->merchant->user->address)
                            ,{{ @$parcel->merchant->user->address }}
                        @endif
                        @if (@$parcel->merchant->user->mobile)
                            ,{{ @$parcel->merchant->user->mobile }}
                        @endif
                        @if (@$parcel->merchant->user->portal_code)
                            ,{{ @$parcel->merchant->user->portal_code }}
                        @endif
                        @if (@$parcel->merchant->user->province->name)
                            {{ @$parcel->merchant->user->province->name }}
                        @endif
                    </p>
                    <p>Recipient:
                        @if (@$parcel->to_merchant->user->name)
                            {{ @$parcel->to_merchant->user->name }}
                        @endif
                        @if (@$parcel->to_merchant->user->address)
                            ,{{ @$parcel->to_merchant->user->address }}
                        @endif
                        @if (@$parcel->to_merchant->user->mobile)
                            ,{{ @$parcel->to_merchant->user->mobile }}
                        @endif
                        @if (@$parcel->to_merchant->user->portal_code)
                            ,{{ @$parcel->to_merchant->user->portal_code }}
                        @endif
                        @if (@$parcel->to_merchant->user->province->name)
                            {{ @$parcel->to_merchant->user->province->name }}
                        @endif
                    </p>
                    @endif
                @endif
                @if(@$parcel->merchant && @$parcel->to_merchant)
                    @if(@$parcel->merchant->account_type == 1 && @$parcel->to_merchant->account_type == 1)
                    <p>Sender:
                        @if (@$parcel->merchant->user->name)
                            {{ @$parcel->merchant->user->name }}
                        @endif
                        @if (@$parcel->merchant->user->address)
                            ,{{ @$parcel->merchant->user->address }}
                        @endif
                        @if (@$parcel->merchant->user->mobile)
                            ,{{ @$parcel->merchant->user->mobile }}
                        @endif
                        @if (@$parcel->merchant->user->portal_code)
                            ,{{ @$parcel->merchant->user->portal_code }}
                        @endif
                        @if (@$parcel->merchant->user->province->name)
                            {{ @$parcel->merchant->user->province->name }}
                        @endif
                    </p>
                    <p>Recipient:
                        @if (@$parcel->to_merchant->user->name)
                            {{ @$parcel->to_merchant->user->name }}
                        @endif
                        @if (@$parcel->to_merchant->user->address)
                            ,{{ @$parcel->to_merchant->user->address }}
                        @endif
                        @if (@$parcel->to_merchant->user->mobile)
                            ,{{ @$parcel->to_merchant->user->mobile }}
                        @endif
                        @if (@$parcel->to_merchant->user->portal_code)
                            ,{{ @$parcel->to_merchant->user->portal_code }}
                        @endif
                        @if (@$parcel->to_merchant->user->province->name)
                            {{ @$parcel->to_merchant->user->province->name }}
                        @endif
                    </p>
                    @endif
                @endif
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
        window.print();
    </script>
</body>
</html>