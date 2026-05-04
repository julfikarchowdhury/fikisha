@extends('backend.partials.master')
@section('title')
{{ __('parcel.holiday') }}
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
                            <li class="breadcrumb-item"><a href="javascript:void(0)" class="breadcrumb-link">{{ __('parcel.holiday') }}</a></li>
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
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('parcel.holiday') }}</h4>
                    @if (hasPermission('holiday_create') == true)
                        <a href="{{route('hrm.holiday.create')}}" class="btn btn-primary btn-sm float-right" title="{{ __('levels.add') }}"><i class="fa fa-plus"></i></a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>{{ __('levels.id') }}</th>
                                    <th>{{ __('levels.name') }}</th>
                                    <th>{{ __('parcel.from_date') }}</th>
                                    <th>{{ __('parcel.to_date') }}</th>
                                    <th>{{ __('parcel.total_days') }}</th>
                                    <th>{{ __('parcel.note') }}</th>
                                    @if (hasPermission('holiday_update') == true || hasPermission('holiday_delete') == true)
                                    <th>{{ __('parcel.action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1; @endphp
                                @foreach($holidays as $holiday)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>{{ @$holiday->name }}</td>
                                    <td>{{ @$holiday->from }}</td>
                                    <td>{{ @$holiday->to }}</td>
                                    <td>{{ @$holiday->total_days }}</td>
                                    <td>{{ @$holiday->note }}</td>
                                    @if (hasPermission('holiday_update') == true || hasPermission('holiday_delete') == true)
                                    <td>
                                        <div class="row">
                                            <button tabindex="-1" data-toggle="dropdown" type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split">
                                                <i class="fa fa-cogs"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                @if (hasPermission('holiday_delete') == true )
                                                <a href="{{route('hrm.holiday.edit',$holiday->id)}}" class="dropdown-item"><i class="fa fa-pen" aria-hidden="true"></i> {{ __('levels.edit') }}</a>
                                                @endif
                                                @if (hasPermission('holiday_delete') == true )
                                                <form id="delete" value="Test" action="{{route('hrm.holiday.delete',$holiday->id)}}" method="POST" data-title="{{ __('parcel.delete_holiday') }}">
                                                    @method('DELETE')
                                                    @csrf
                                                    <button type="submit" class="dropdown-item"><i class="fa fa-trash" aria-hidden="true"></i> {{ __('levels.delete') }}</button>
                                                </form>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>

                    <div class="pb-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <span>{{ $holidays->links() }}</span>
                        <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
                            {!! __('Showing') !!}
                            <span class="font-medium">{{ $holidays->firstItem() }}</span>
                            {!! __('to') !!}
                            <span class="font-medium">{{ $holidays->lastItem() }}</span>
                            {!! __('of') !!}
                            <span class="font-medium">{{ $holidays->total() }}</span>
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
@endsection