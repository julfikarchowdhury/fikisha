@extends('backend.deliveryman_panel.dashboard')
@section('dashboard-tab-content')
<!-- pageheader  -->
<div class="row pl-4 pr-4 pt-4 merchantParcelPage">
    <div class="col-8">
        <div class="d-flex parcelsearchFlex parcel-import-export-btn">
            <p class="h3 mr-5">{{ __('parcel.title') }} </p>
        </div>
    </div>
</div>
<div class="table-responsive">
    <table id="table" class="table" style="width:100%">
        <thead>
            <tr>
                <th class="parcel-index permission-check-box">
                    <input type="checkbox" id="tick-all" class="form-check-input"/>
                </th>
                <th>{{ __('###') }}</th>
                <th>{{ __('parcel.tracking_id') }}</th>
                <th>{{ __('parcel.recipient_info') }}</th>
                <th>{{ __('parcel.amount')}}</th>
                <th>{{ __('parcel.status') }}</th>
                <th>{{ __('parcel.payment')}}</th>
            </tr>
        </thead>
        <tbody>
            @php $i=1; @endphp
            @foreach($parcels as $parcel)
            <tr>
                <td class="parcel-index permission-check-box">
                    <input type="checkbox" name="parcels[][{{ $parcel->id }}]" value="{{ $parcel->id }}" class="common-key form-check-input" />
                </td>
                <td>
                    <div class="row">
                        <button tabindex="-1" data-toggle="dropdown" type="button" class="btn btn-sm ml-2 bnone">...</button>
                        <div class="dropdown-menu">
                            <a href="{{ route('merchant-panel.parcel.details',$parcel->id) }}" class="dropdown-item"><i class="fa fa-eye" aria-hidden="true"></i> {{__('levels.view')}}</a>
                            <a href="{{ route('deliveryman.parcel.logs',$parcel->id) }}" class="dropdown-item"><i class="fas fa-history" aria-hidden="true"></i> {{__('levels.parcel_logs')}}</a>

                        </div>
                    </div>
                </td>
                <td>{{ $parcel->tracking_id }}</td>
                <td class="merchantpayment">
                    <div class="w150">
                        <div class="d-flex">
                            <i class="fa fa-user"></i>&nbsp;<p>{{$parcel->customer_name}}</p>
                        </div>
                        <div class="d-flex">
                            <i class="fas fa-phone"></i>&nbsp;<p>{{$parcel->customer_phone}}</p>
                        </div>
                        <div class="d-flex">
                            <i class="fas fa-map-marker-alt"></i>&nbsp;<p>{{$parcel->customer_address}}</p>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="w250">
                        {{__('levels.cod')}}: <span class="text-dark">{{settings()->currency}}{{$parcel->cash_collection}}</span>
                        <br>
                        {{__('levels.total_delivery_amount')}}: <span class="text-dark">{{settings()->currency}}{{$parcel->total_delivery_amount}}</span>
                        <br>
                        {{__('levels.vat_amount')}}: <span class="text-dark">{{settings()->currency}}{{$parcel->vat_amount}}</span>
                        <br>
                        {{__('levels.current_payable')}}: <b>{{settings()->currency}}{{$parcel->current_payable}}</b>
                        <br>
                    </div>
                </td>
                 <td>
                    <p>{!! $parcel->parcel_status !!}</p>
                    <span>{{__('parcel.updated_on')}}: {{\Carbon\Carbon::parse($parcel->updated_at)->format('Y-m-d h:i:s A')}}</span>
                </td>
                <td>
                    @if ($parcel->status == \App\Enums\ParcelStatus::DELIVERED)
                        <a href="{{ route('parcel.deliveredInfo', $parcel->id) }}"
                            class="btn btn-sm btn-warning ml-1 " data-toggle="tooltip"
                            data-placement="top" title="View">{{ __('View Proof') }}</a>
                    @endif
                    
                    @if ($parcel->invoice)
                           <p class="mb-0">{{ __('invoice.'.@$parcel->invoice->status) }}</p>
                            {{ @$parcel->invoice->invoice_id }}<br/>
                        @if ($parcel->invoice->status == App\Enums\InvoiceStatus::PAID)
                            Paid At: {{ @dateFormat(@$parcel->invoice->updated_at) }}
                        @endif
                    @else
                        N/A
                    @endif 
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<div class="pb-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
    <span>{{ $parcels->appends($request->all())->links() }}</span>
    <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
        {!! __('Showing') !!}
        <span class="font-medium">{{ $parcels->firstItem() }}</span>
        {!! __('to') !!}
        <span class="font-medium">{{ $parcels->lastItem() }}</span>
        {!! __('of') !!}
        <span class="font-medium">{{ $parcels->total() }}</span>
        {!! __('results') !!}
    </p>
</div>
@endsection
