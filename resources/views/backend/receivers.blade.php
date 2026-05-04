@extends('backend.partials.master')
@section('title')
{{ __('levels.receivers') }} {{ __('levels.list') }}
@endsection
@section('maincontent')
<div class="container-fluid  dashboard-content">
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="page-header">
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard.index')}}" class="breadcrumb-link">{{ __('levels.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{ __('levels.receivers') }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('levels.receivers') }}</h4>
                    <div class="d-flex justify-content-start mt-md-0 d-lg-block   ">
                        <a href="{{ route('receiver.export', ['parcel_date' => $request->parcel_date, 'parcel_status' => $request->parcel_status, 'parcel_customer' => $request->parcel_customer, 'parcel_customer_phone' => $request->parcel_customer_phone]) }}"
                            class="btn btn-success btn-sm " data-toggle="tooltip" data-placement="top"
                            title="Add"> <i class="fa fa-download"></i> {{ __('parcel.export_xlsx') }} Download</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table   " style="width:100%">
                            <thead>
                                <tr>
                                    <th>{{ __('to_do.sl') }}</th>
                                    <th>{{ __('levels.name') }}</th>
                                    <th>{{ __('levels.phone') }}</th>
                                    <th>{{ __('levels.address') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1; @endphp
                                @foreach($receivers as $receiver)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td> {{ $receiver->name }}</td>
                                    <td> {{ $receiver->phone }}</td>
                                    <td> {{ $receiver->address }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="pb-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span>{{ $receivers->links() }}</span>
                    <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
                        {!! __('Showing') !!}
                        <span class="font-medium">{{ $receivers->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-medium">{{ $receivers->lastItem() }}</span>
                        {!! __('of') !!}
                        <span class="font-medium">{{ $receivers->total() }}</span>
                        {!! __('results') !!}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection()