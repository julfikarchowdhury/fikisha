@extends('backend.partials.master')
@section('title')
    {{ __('parcel.duty_schedule') }} {{ __('levels.edit') }}
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
                            <li class="breadcrumb-item"><a href="{{ route('hrm.duty.schedule.index') }}" class="breadcrumb-link">{{ __('parcel.leave_type') }}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{ __('levels.edit') }}</a></li>
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
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('parcel.duty_schedule') }} {{ __('levels.edit') }}</h4>
                </div>
                <div class="card-body">
                    <!-- <h2 class="pageheader-title">{{ __('parcel.duty_schedule') }} {{ __('levels.edit') }}</h2> -->
                    <form action="{{route('hrm.duty.schedule.update',['id'=>$duty_schedule->id])}}"  method="POST" enctype="multipart/form-data" id="basicform">
                        @csrf
                        @method('put')
                        <div class="row"> 
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="title">{{ __('role.title') }}</label> <span class="text-danger">*</span>
                                    <select class="form-control select2" name="role_id" >
                                        <option value="">{{ __('menus.select') }} {{ __('role.title') }}</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}" @if (old('role_id',$duty_schedule->role_id) == $role->id) selected @endif>{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('role_id')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="start_time" class="form-label">{{ __('parcel.start_time') }}  <span class="text-danger">*</span></label>
                                <input type="time"  name="start_time" class="form-control" id="start_time" value="{{ old('start_time',$duty_schedule->start_time) }}">
                                @error('start_time')
                                    <p class="text-danger pt-2">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="end_time" class="form-label">{{ __('parcel.end_time') }}  <span class="text-danger">*</span></label>
                                <input type="time"  name="end_time" class="form-control" id="end_time" value="{{ old('end_time',$duty_schedule->end_time) }}">
                                @error('end_time')
                                    <p class="text-danger pt-2">{{ $message }}</p>
                                @enderror
                            </div> 

                        </div>

                        <div class="row mt-3">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 ">
                                <button type="submit" class="btn btn-space btn-primary">{{ __('levels.update') }}</button>
                                <a href="{{ route('hrm.duty.schedule.index') }}" class="btn btn-space btn-secondary">{{ __('levels.cancel') }}</a>
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
 
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush