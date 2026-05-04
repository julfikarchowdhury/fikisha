@extends('backend.statistics.statistics')
@section('title')
{{ __('deliveryman.title') }} {{ __('merchant.statistics') }}
@endsection
@section('statistics-content')

<div class="table-responsive">
   <table class="table   " style="width:100%">
      <thead>
         <tr>
            <th>{{ __('levels.id') }}</th>
            <th>{{ __('levels.details') }}</th>
            <th>{{ __('levels.hub') }}</th>
            <th>{{ __('levels.total_cash_collection') }}</th>
            <th>{{ __('levels.total_income') }}</th>
            <th>{{ __('levels.total_paid_to_hub') }}</th>
            <th>{{ __('levels.total_payable_amount') }}</th>
         </tr>
      </thead>
      <tbody>
         @if(!blank($deliverymans))
         @php $i=1; @endphp
         @foreach($deliverymans as $deliveryman)
         <tr>
            <td>{{$i++}}</td>
            <td>
               <div class="row">
                  <div class="col-md-3">
                     <img src="{{$deliveryman->user->image}}" alt="user" class="rounded" width="40" height="40">
                  </div>
                  <div class="col-md-9">
                     <strong>{{$deliveryman->user->name}}</strong>
                     <p>{{$deliveryman->user->email}}</p>
                  </div>
               </div>
            </td>
            <td>@if(isset($deliveryman->user->hub->name)) {{ $deliveryman->user->hub->name }} @endif </td>
            <td>{{ settings()->currency }} {{ $deliveryman->totalCashCollection->sum('amount') }} </td>
            <td>{{ settings()->currency }} {{ $deliveryman->totalIncomes->sum('amount') }} </td>
            <td>{{ settings()->currency }} {{ $deliveryman->totalPaidToCourier->sum('amount') }} </td>
            <td>{{ settings()->currency }} {{ $deliveryman->totalExpense->sum('amount') - $deliveryman->totalIncome->sum('amount') }} </td>
         </tr>
         @endforeach
         @endif
      </tbody>
   </table>
</div>
<div class="pb-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
   <span>{{ $deliverymans->links() }}</span>
   <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
      {!! __('Showing') !!}
      <span class="font-medium">{{ $deliverymans->firstItem() }}</span>
      {!! __('to') !!}
      <span class="font-medium">{{ $deliverymans->lastItem() }}</span>
      {!! __('of') !!}
      <span class="font-medium">{{ $deliverymans->total() }}</span>
      {!! __('results') !!}
   </p>
</div>

@endsection