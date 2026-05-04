@extends('backend.partials.master')
@section('title')
    {{ __('parcel.leave_assign') }}  
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
                            <li class="breadcrumb-item"><a href="javascript:void(0)" class="breadcrumb-link">{{ __('parcel.leave_assign') }}</a></li>
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
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('parcel.leave_assign') }}</h4>
                    @if (hasPermission('leave_assign_create') == true)
                    <a href="{{route('hrm.leave.assign.create')}}" class="btn btn-primary btn-sm float-right" title="{{ __('levels.add') }}"><i class="fa fa-plus"></i></a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" style="width:100%">
                            <thead>
                                <tr> 
                                    <th>{{ __('levels.id') }}</th> 
                                    <th>{{ __('parcel.leave_type') }}</th>
                                    <th>{{ __('role.title') }}</th>
                                    <th>{{ __('parcel.days') }}</th> 
                                    <th>{{ __('levels.status') }}</th>  
                                    @if (hasPermission('leave_assign_update') == true || hasPermission('leave_assign_delete') == true)
                                        <th>{{ __('parcel.action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1; @endphp
                                @foreach($leave_assigns as $leave_assign)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>{{ @$leave_assign->leavetype->name }}</td>
                                    <td>{{ @$leave_assign->role->name }}</td>
                                    <td>{{ @$leave_assign->days }}</td>
                                    <td>{!! @$leave_assign->my_status !!}</td> 
                                    @if ( hasPermission('leave_assign_update') == true || hasPermission('leave_assign_delete') == true)
                                        <td>
                                            <div class="row">
                                                <button tabindex="-1" data-toggle="dropdown" type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split"> 
                                                    <i class="fa fa-cogs"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                    @if (hasPermission('leave_assign_update') == true )
                                                        <a href="{{route('hrm.leave.assign.edit',$leave_assign->id)}}" class="dropdown-item"><i class="fas fa-edit" aria-hidden="true"></i> {{ __('levels.edit') }}</a>
                                                    @endif
                                                    @if (hasPermission('leave_assign_delete') == true  )
                                                        <form id="delete" value="Test" action="{{route('hrm.leave.assign.delete',$leave_assign->id)}}" method="POST" data-title="{{ __('parcel.delete_leave_assign') }}">
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
                </div>

                <div class="pb-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span>{{ $leave_assigns->links() }}</span>
                    <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
                        {!! __('Showing') !!}
                        <span class="font-medium">{{ $leave_assigns->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-medium">{{ $leave_assigns->lastItem() }}</span>
                        {!! __('of') !!}
                        <span class="font-medium">{{ $leave_assigns->total() }}</span>
                        {!! __('results') !!}
                    </p>
                </div>
            </div>
        </div>
        <!-- end table  -->
    </div>
</div>
<!-- end wrapper  -->

@endsection()
