@extends('backend.partials.master')
@section('title')
{{ __('parcel.leave') }}
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
                            <li class="breadcrumb-item"><a href="javascript:void(0)" class="breadcrumb-link">{{ __('parcel.leave') }}</a></li>
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
                    <form action="{{route('hrm.leave.index')}}" method="GET">
                        <input type="hidden" name="filter" value="true" />
                        <div class="row">
                            <div class="form-group col-12 col-sm-6 col-md-4 col-lg-4 col-xl-2">
                                <label for="date">{{ __('parcel.date') }}</label>
                                <input type="text" autocomplete="off" id="date" name="date" placeholder="Enter Date" class="form-control date_range_picker" value="{{ old('date',$request->date) }}">
                                @error('date')
                                <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group col-12 col-xl-3 col-lg-4 col-md-6">
                                <label for="user_id">{{ __('parcel.user') }}</label>
                                <select id="user_id" name="user_id" class="form-control select2">
                                    <option selected disabled>{{ __('menus.select') }} {{ __('parcel.user') }}</option>
                                    @foreach ($users as $user)
                                    <option value="{{ $user->id }}" @if(old('user_id',$request->user_id) == $user->id) selected @endif>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-12 col-xl-3 col-lg-4 col-md-6">
                                <label for="status">{{ __('levels.status') }}</label> <span class="text-danger">*</span>
                                <select name="status" class="form-control select2">
                                    <option disabled selected>{{ __('menus.select') }} {{ __('levels.status') }}</option>
                                    @foreach (__('LeaveStatus') as $key=>$leaveStatus)
                                    <option value="{{ $key }}" @if(old('status',$request->status) == $key) selected @endif>{{ $leaveStatus }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-12 col-xl-3 col-lg-4 col-md-6 pt-1">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 pt-4 d-flex justify-content pl-0">
                                    <button type="submit" class="btn btn-space btn-primary"><i class="fa fa-filter"></i> {{ __('levels.filter') }}</button>
                                    <a href="{{ route('hrm.leave.index') }}" class="btn btn-space btn-secondary"><i class="fa fa-eraser"></i> {{ __('levels.clear') }}</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>



            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('parcel.leave') }}</h4>
                    @if (hasPermission('leave_create') == true)
                    <a href="{{route('hrm.leave.create')}}" class="btn btn-primary btn-sm float-right" title="{{ __('levels.add') }}"><i class="fa fa-plus"></i></a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>{{ __('levels.id') }}</th>
                                    <th>{{ __('parcel.applicant') }}</th>
                                    <th>{{ __('parcel.leave_type') }}</th>
                                    <th>{{ __('parcel.leave_from') }}</th>
                                    <th>{{ __('parcel.leave_to') }}</th>
                                    <th>{{ __('parcel.file') }}</th>
                                    <th>{{ __('parcel.reason') }}</th>
                                    <th>{{ __('parcel.status') }}</th>
                                    <th>{{ __('parcel.submited') }}</th>
                                    @if (hasPermission('leave_delete') == true || hasPermission('leave_approval') == true)
                                    <th>{{ __('parcel.action') }}</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1; @endphp
                                @foreach($leaves as $leave)
                                <tr>
                                    <td>{{ $i++ }} </td>
                                    <td>{{ @$leave->user->name }}</td>
                                    <td>{{ @$leave->leaveType->name; }}</td>
                                    <td>{{ @dateFormat2($leave->leave_from) }}</td>
                                    <td>{{ @dateFormat2($leave->leave_to) }}</td>
                                    <td>
                                        <a href="{{ @$leave->file_path }}" download="">{{ __('levels.download') }}</a>
                                    </td>
                                    <td>{{ @$leave->reason }}</td>
                                    <td>{!! @$leave->my_status !!}</td>
                                    <td>{{ @dateFormat2($leave->created_at) }}</td>
                                    @if (hasPermission('leave_delete') == true || hasPermission('leave_approval') == true )
                                    <td>
                                        @if($leave->status == App\Enums\LeaveStatus::PENDING)
                                        <div class="row">
                                            <button tabindex="-1" data-toggle="dropdown" type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split">
                                                <i class="fa fa-cogs"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                @if (hasPermission('leave_approval') == true )
                                                @if($leave->status == App\Enums\LeaveStatus::PENDING)
                                                <a href="{{route('hrm.leave.approval',['id'=>$leave->id,'status'=>\App\Enums\LeaveStatus::APPROVED])}}" class="dropdown-item"><i class="fas fa-check" aria-hidden="true"></i> {{ __('parcel.approved') }}</a>
                                                <a href="{{route('hrm.leave.approval',['id'=>$leave->id,'status'=>\App\Enums\LeaveStatus::REJECTED])}}" class="dropdown-item"><i class="fas fa-close" aria-hidden="true"></i> {{ __('parcel.rejected') }}</a>
                                                @endif
                                                @endif
                                                @if (hasPermission('leave_delete') == true && $leave->status == App\Enums\LeaveStatus::PENDING )
                                                <form id="delete" value="Test" action="{{route('hrm.leave.delete',$leave->id)}}" method="POST" data-title="{{ __('parcel.delete_leave') }}">
                                                    @method('DELETE')
                                                    @csrf
                                                    <button type="submit" class="dropdown-item"><i class="fa fa-trash" aria-hidden="true"></i> {{ __('levels.delete') }}</button>
                                                </form>
                                                @endif
                                            </div>
                                        </div>
                                        @else
                                        <i class="fa fa-ellipsis"></i>
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>

                <div class="pb-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span>{{ $leaves->links() }}</span>
                    <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
                        {!! __('Showing') !!}
                        <span class="font-medium">{{ $leaves->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-medium">{{ $leaves->lastItem() }}</span>
                        {!! __('of') !!}
                        <span class="font-medium">{{ $leaves->total() }}</span>
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
@push('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@push('scripts')

<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="{{ static_asset('backend/js/date-range-picker/date-range-picker-custom.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush