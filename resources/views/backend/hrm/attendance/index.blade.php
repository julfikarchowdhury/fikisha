@extends('backend.partials.master')
@section('title')
    {{ __('parcel.attendance') }}  
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
                            <li class="breadcrumb-item"><a href="javascript:void(0)" class="breadcrumb-link">{{ __('parcel.attendance') }}</a></li>
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
                <div class="card-body">
                    <form action="{{route('hrm.attendance.filter')}}"  method="GET">
                        @csrf
                        <div class="row">
                            <div class="form-group col-12 col-xl-3 col-lg-4 col-md-6" >
                                <label for="search">{{ __('levels.search') }}</label>
                                <input type="text" id="search" name="search" placeholder="{{ __('parcel.Enter_search') }}"  class="form-control" value="{{old('search', $request->search)}}">
                            </div>
  
                            <div class="form-group col-12 col-xl-3 col-lg-4 col-md-6 pt-1">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 pt-4 d-flex justify-content pl-0">
                                    <button type="submit" class="btn btn-space btn-primary"><i class="fa fa-filter"></i> {{ __('levels.filter') }}</button>
                                    <a href="{{ route('hrm.attendance.index') }}" class="btn btn-space btn-secondary"><i class="fa fa-eraser"></i> {{ __('levels.clear') }}</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('parcel.attendance') }}</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" style="width:100%">
                            <thead>
                                <tr> 
                                    <th>{{ __('levels.id') }}</th>
                                    <th>{{ __('levels.user') }}</th>  
                                    @foreach ($data['full_month_dates'] as $date)
                                        <th>{{ @dateDay($date)['d'] }} <br/> {{ @dateDay($date)['D'] }}</th>
                                    @endforeach
                                    <th>{{ __('parcel.total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i=0;
                                @endphp
                                @foreach ($users as $user) 
                                    <tr>
                                        <td data-title="#">{{ ++$i }}</td>
                                        <td data-title="{{ __('employee_name') }}">
                                            <div class="row text-left">
                                                <div  >
                                                    <img src="{{ @$user->image }}" width="60" class="rounded-circle"/>
                                                </div>
                                                <div  >
                                                    <strong>{{@$user->name}}</strong>
                                                    <p> {{ @$user->email }}</p>
                                                </div>
                                            </div> 
                                        </td>
                                        @foreach ($data['full_month_dates'] as $date) 
                                            <td data-title="{{ @dateDay($date)['d'] }} {{ @dateDay($date)['D'] }}day"> 
                                                @if(\Carbon\Carbon::parse($date)->format('d') <= date('d'))
                                                    {!! @dayAttendance($user->id,$date)  !!}
                                                @else
                                                -
                                                @endif
                                            </td>
                                        @endforeach
                                        <td data-title="{{ __('total') }}"> 
                                            <div>
                                                <span class="text-primary">{{ totalPresent($user->id,$data) }}</span>/ {{ @$data['total_month_days'] }} 
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>

                    
                    <div class="pb-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <span>{{ $users->links() }}</span>
                        <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
                            {!! __('Showing') !!}
                            <span class="font-medium">{{ $users->firstItem() }}</span>
                            {!! __('to') !!}
                            <span class="font-medium">{{ $users->lastItem() }}</span>
                            {!! __('of') !!}
                            <span class="font-medium">{{ $users->total() }}</span>
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
