@extends('backend.partials.master')
@section('title')
{{ __('parcel.apply_leave') }}
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
                            <li class="breadcrumb-item"><a href="javascript:void(0)" class="breadcrumb-link">{{ __('parcel.apply_leave') }}</a></li>
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
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('parcel.available_leaves') }}</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>{{ __('parcel.leave_type') }}</th>
                                    <th>{{ __('parcel.total_days') }}</th>
                                    <th>{{ __('parcel.remaining_days') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1; @endphp
                                @foreach($available_leavs as $available_leave)
                                <tr>
                                    <td>{{ @$available_leave->leaveType->name; }}</td>
                                    <td>{{ @$available_leave->days }}</td>
                                    <td> {{ @MyLeave($available_leave->id,Auth::user()->id,Auth::user()->role_id) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="pb-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span>{{ $available_leavs->links() }}</span>
                    <p class="mb-0" style="font-size: 14px; font-weight: 400; color: #333333;">
                        {!! __('Showing') !!}
                        <span class="font-medium">{{ $available_leavs->firstItem() }}</span>
                        {!! __('to') !!}
                        <span class="font-medium">{{ $available_leavs->lastItem() }}</span>
                        {!! __('of') !!}
                        <span class="font-medium">{{ $available_leavs->total() }}</span>
                        {!! __('results') !!}
                    </p>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <form action="{{route('hrm.apply.leave.index')}}" method="GET">
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
                                <label for="status">{{ __('levels.status') }}</label> <span class="text-danger">*</span>
                                <select name="status" class="form-control select2">

                                    <option value="">{{ __('menus.select') }} {{ __('levels.status') }}</option>
                                    @foreach (__('LeaveStatus') as $key=>$leaveStatus)
                                    <option value="{{ $key }}" @if(old('status',$request->status) == $key) selected @endif>{{ $leaveStatus }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-12 col-xl-3 col-lg-4 col-md-6 pt-1">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 pt-4 d-flex justify-content pl-0">
                                    <button type="submit" class="btn btn-space btn-primary"><i class="fa fa-filter"></i> {{ __('levels.filter') }}</button>
                                    <a href="{{ route('hrm.apply.leave.index') }}" class="btn btn-space btn-secondary"><i class="fa fa-eraser"></i> {{ __('levels.clear') }}</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('parcel.apply_leave') }}</h4>
                    <div class="btn-group">
                        <a href="{{route('hrm.apply.leave.create')}}" class="btn btn-primary btn-sm" title="{{ __('levels.add') }}"><i class="fa fa-plus"></i></a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" style="width:100%">
                            <thead>
                                <tr>
                                    <th>{{ __('levels.id') }}</th>
                                    <th>{{ __('parcel.leave_type') }}</th>
                                    <th>{{ __('parcel.leave_from') }}</th>
                                    <th>{{ __('parcel.leave_to') }}</th>
                                    <th>{{ __('parcel.file') }}</th>
                                    <th>{{ __('parcel.reason') }}</th>
                                    <th>{{ __('parcel.status') }}</th>
                                    <th>{{ __('parcel.submited') }}</th>
                                    <th>{{ __('parcel.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i=1; @endphp
                                @foreach($leaves as $leave)
                                <tr>
                                    <td>{{ $i++ }} </td>
                                    <td>{{ @$leave->leaveType->name; }}</td>
                                    <td>{{ @dateFormat2($leave->leave_from) }}</td>
                                    <td>{{ @dateFormat2($leave->leave_to) }}</td>
                                    <td> <a href="{{ $leave->file_path }}" download="">{{ __('levels.download') }}</a> </td>
                                    <td>{{ @$leave->reason }}</td>
                                    <td>{!! @$leave->my_status !!}</td>
                                    <td>{{ @dateFormat2($applied_leave->created_at) }}</td>
                                    <td>
                                        @if($leave->status == App\Enums\LeaveStatus::PENDING)
                                        <div class="row">
                                            <button tabindex="-1" data-toggle="dropdown" type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split">
                                                <i class="fa fa-cogs"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <form id="delete" value="Test" action="{{route('hrm.apply.leave.delete',$leave->id)}}" method="POST" data-title="{{ __('parcel.delete_apply_leave') }}">
                                                    @method('DELETE')
                                                    @csrf
                                                    <button type="submit" class="dropdown-item"><i class="fa fa-trash" aria-hidden="true"></i> {{ __('levels.delete') }}</button>
                                                </form>
                                            </div>
                                        </div>
                                        @else
                                        <i class="fa fa-ellipsis"></i>
                                        @endif
                                    </td>
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