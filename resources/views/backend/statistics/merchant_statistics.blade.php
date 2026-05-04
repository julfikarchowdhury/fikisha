@extends('backend.statistics.statistics')
@section('title')
   {{ __('merchant.title') }}  {{ __('merchant.statistics') }}
@endsection
 
@section('statistics-content')
   
        <div class="table-responsive">
            <table id="table" class="table   " style="width:100%">
                <thead>
                    <tr>
                        <th>{{ __('levels.id') }}</th>
                        <th>{{ __('levels.details') }}</th>
                        <th>{{ __('levels.business_name') }}</th> 
                        <th>{{ __('levels.total_parcels') }}</th> 
                        <th>{{ __('levels.total_delivered') }}</th> 
                        <th>{{ __('levels.payable_balance') }}</th> 
                        <th>{{ __('levels.total_paid_amount') }}</th> 
                        <th>{{ __('levels.total_due_amount') }}</th> 
                    </tr>
                </thead>
                <tbody>
                    @php $i=1; @endphp
                    @foreach($merchants as $merchant)
                        <tr>
                            <td>{{$i++}}</td>
                            <td>
                                <div class="row">
                                    <div class="pr-3">
                                        <img src="{{$merchant->user->image}}" alt="user" class="rounded" width="40" height="40">
                                    </div>
                                    <div>
                                        <strong>{{$merchant->user->name}}</strong>
                                        <p class="mb-0">{{$merchant->user->email}}</p>
                                        <p>{{@$merchant->user->mobile}}</p>
                                    </div>
                                </div>
                            </td>
                            <td>{{@$merchant->business_name}}</td>  
                            <td>{{@$merchant->parcels->count()}}</td>  
                            <td>{{@$merchant->deliveredParcels->count()}}</td>  
                            <td>{{ settings()->currency }} {{@$merchant->deliveredParcels->sum('current_payable')}}</td>  
                            <td>{{ settings()->currency }} {{@$merchant->totalProcessedPayments->sum('amount')}}</td>  
                            <td>{{ settings()->currency }} {{($merchant->deliveredParcels->sum('current_payable') - $merchant->totalProcessedPayments->sum('amount'))}}</td>  
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pb-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span>{{ $merchants->links() }}</span>
            <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
                {!! __('Showing') !!}
                <span class="font-medium">{{ $merchants->firstItem() }}</span>
                {!! __('to') !!}
                <span class="font-medium">{{ $merchants->lastItem() }}</span>
                {!! __('of') !!}
                <span class="font-medium">{{ $merchants->total() }}</span>
                {!! __('results') !!}
            </p>
        </div>
 
@endsection