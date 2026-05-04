@extends('backend.partials.master')
@section('title')
    {{ __('parcel.weekend') }}  
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
                            <li class="breadcrumb-item"><a href="javascript:void(0)" class="breadcrumb-link">{{ __('parcel.weekend') }}</a></li>
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
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('parcel.weekend') }}</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" style="width:100%">
                            <thead>
                                <tr> 
                                    <th>{{ __('levels.id') }}</th>
                                    <th>{{ __('levels.name') }}</th>
                                    <th>{{ __('parcel.is_weekend') }}</th>
                                    @if (hasPermission('weekend_update') == true )
                                    <th>{{ __('parcel.action') }}</th>   
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1; @endphp
                                @foreach($weekends as $weekend)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>{{ @$weekend->name }}</td> 
                                    <td>{!! @$weekend->my_status !!}</td>  
                                    @if (hasPermission('weekend_update') == true )
                                        <td> 
                                            <div class="row">
                                                <button tabindex="-1" data-toggle="dropdown" type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split"> 
                                                    <i class="fa fa-cogs"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    <a href="{{route('hrm.weekend.update',['id'=>$weekend->id,'is_weekend'=>App\Enums\WeekendStatus::YES])}}" class="dropdown-item"><i class="fa fa-check" aria-hidden="true"></i> {{ __('parcel.yes') }}</a>
                                                    <a href="{{route('hrm.weekend.update',['id'=>$weekend->id,'is_weekend'=>App\Enums\WeekendStatus::NO])}}" class="dropdown-item"><i class="fas fa-times" aria-hidden="true"></i> {{ __('parcel.no') }}</a>
                                                </div>
                                            </div>
                                        </td> 
                                    @endif  
                                </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
 
            </div>
        </div>
        <!-- end table  -->
    </div>
</div>
<!-- end wrapper  -->

@endsection()
