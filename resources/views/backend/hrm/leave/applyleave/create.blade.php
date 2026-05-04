@extends('backend.partials.master')
@section('title')
    {{ __('parcel.apply_leave') }} {{ __('levels.create') }}
@endsection
@section('maincontent')
<div class="container-fluid  dashboard-content">
    <!-- pageheader -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="page-header">
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard.index')}}" class="breadcrumb-link">{{ __('levels.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('hrm.apply.leave.index') }}" class="breadcrumb-link">{{ __('parcel.apply_leave') }}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{ __('levels.create') }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- end pageheader -->

    <div class="row">
        <!-- basic form -->
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('parcel.apply_leave') }} {{ __('levels.create') }}</h4>
                </div>
                <div class="card-body">
                    <!-- <h2 class="pageheader-title">{{ __('parcel.apply_leave') }} {{ __('levels.create') }}</h2> -->
                    <form action="{{route('hrm.apply.leave.store')}}"  method="POST" enctype="multipart/form-data" id="basicform">
                        @csrf 
                        <div class="row mt-3"> 
                            
                            <div class="col-lg-6 mt-2">
                                <label for="leave_assign_id" class="form-label">{{ __('parcel.leave_type') }} <span class="text-danger">*</span></label>
                               <select class="form-control  select2" name="leave_assign_id" id="leave_assign_id">
                                   <option selected disabled>{{ __('menus.select') }} {{ __('parcel.leave_type') }}</option> 
                                    @php 
                                        $assignedLeave = App\Models\Backend\HRM\LeaveAssign::where(['role_id'=>Auth::user()->role_id,'status'=>App\Enums\Status::ACTIVE])->whereYear('created_at',Date('Y'))->get(); 
                                    @endphp
                                    @foreach ($assignedLeave as $leave_assign)
                                            <option value="{{ @$leave_assign->id }}" @if (old('leave_assign_id') == $leave_assign->id) selected @endif>{{ @$leave_assign->leaveType->name }}</option>
                                    @endforeach  
                               </select>
                                @error('leave_assign_id')
                                    <p class="text-danger pt-2">{{ $message }}</p>
                                @enderror
                            </div>
 
                            <div class="col-lg-6 mt-2">
                                <label for="leave_from" class="form-label">{{ __('parcel.leave_from') }} <span class="text-danger">*</span></label> 
                                <input type="date" name="leave_from"  class="form-control " id="leave_from" value="{{ old('leave_from',old('leave_from',date('Y-m-d'))) }}"  >
                                @error('leave_from')
                                    <p class="text-danger pt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-lg-6 mt-2">
                                <label for="leave_to" class="form-label">{{ __('parcel.leave_to') }} <span class="text-danger">*</span></label>
                                <input type="date" name="leave_to" class="form-control  dateformat2" id="leave_to" value="{{ old('leave_to',old('leave_to')) }}" >
                                @error('leave_to')
                                    <p class="text-danger pt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-lg-6 mt-2">
                                <label for="file" class="form-label">{{ __('parcel.file') }} </label>
                                <input type="file" name="file" class="form-control  " id="file">
                            </div>

                            <div class="col-lg-6 mt-2">
                                <label for="reason" class="form-label">{{ __('parcel.reason') }}  </label>
                                <textarea name="reason" class="form-control ">{{ old('reason') }}</textarea>
                            </div>
 
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 mt-3">
                                <button type="submit" class="btn btn-space btn-primary">{{ __('levels.save') }}</button>
                                <a href="{{ route('hrm.apply.leave.index') }}" class="btn btn-space btn-secondary">{{ __('levels.cancel') }}</a>
                            </div> 
                        </div>
  
                    </form>
                </div>
            </div>
        </div>
        <!-- end basic form -->
    </div>
</div>
@endsection()
 
<!-- css  -->
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> 
@endpush
<!-- js  -->
@push('scripts') 
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> 
 @endpush
