@extends('backend.partials.master')
@section('title')
    {{ __('parcel.leave_assign') }} {{ __('levels.add') }}
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
                            <li class="breadcrumb-item"><a href="{{ route('hrm.leave.assign.index') }}" class="breadcrumb-link">{{ __('parcel.leave_assign') }}</a></li>
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
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('parcel.leave_assign') }} {{ __('levels.create') }}</h4>
                </div>
                <div class="card-body">
                    <!-- <h2 class="pageheader-title">{{ __('parcel.leave_assign') }} {{ __('levels.create') }}</h2> -->
                    <form action="{{route('hrm.leave.assign.store')}}"  method="POST" enctype="multipart/form-data" id="basicform">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="role_id">{{ __('role.title') }}</label> <span class="text-danger">*</span>
                                     <select class="form-control select2" name="role_id">
                                        <option selected disabled>{{ __('levels.select') }} {{ __('role.title') }}</option>
                                        @foreach ($roles as $role )
                                            <option value="{{ $role->id }}" @if(old('role_id') == $role->id) selected @endif >{{ $role->name }}</option>
                                        @endforeach
                                     </select>
                                    @error('role_id')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
  
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="leave_type">{{ __('parcel.leave_type') }}</label> <span class="text-danger">*</span>
                                    <select class="form-control select2" name="type_id">
                                        <option selected disabled>{{ __('levels.select') }} {{ __('parcel.leave_type') }}</option>
                                        @foreach ($leave_types as $leave_type )
                                            <option value="{{ $leave_type->id }}" @if(old('type_id') == $leave_type->id) selected @endif>{{ $leave_type->name }}</option>
                                        @endforeach
                                     </select>
                                    @error('type_id')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="days">{{ __('parcel.days') }}</label> <span class="text-danger">*</span>
                                   <input type="text" class="form-control" name="days" placeholder="{{ __('parcel.enter_days') }}" value="{{ old('days') }}"/>
                                    @error('days')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6"> 
                                <div class="form-group">
                                    <label for="status">{{__('levels.status')}}</label> <span class="text-danger">*</span>
                                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                                        @foreach(trans('status') as $key => $status)
                                            <option value="{{ $key }}" {{ (old('status',\App\Enums\Status::ACTIVE) == $key) ? 'selected' : '' }}>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div> 
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 ">
                                <button type="submit" class="btn btn-space btn-primary">{{ __('levels.save') }}</button>
                                <a href="{{ route('hrm.leave.assign.index') }}" class="btn btn-space btn-secondary">{{ __('levels.cancel') }}</a>
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
