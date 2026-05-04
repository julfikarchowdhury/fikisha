@extends('backend.partials.master')
@section('title')
    {{ __('parcel.zone') }} {{ __('levels.edit') }}
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
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">{{__('menus.settings')}}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('settings.zone.delivery-charge.index') }}" class="breadcrumb-link">{{ __('parcel.zone') }}</a></li>
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
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('parcel.zone') }} {{ __('levels.edit') }}</h4>
                </div>
                <div class="card-body">
                    <!-- <h2 class="pageheader-title">{{ __('parcel.zone') }} {{ __('levels.edit') }}</h2> -->
                    <form action="{{route('settings.zone.delivery-charge.update',['id'=>$zone->id])}}"  method="POST" enctype="multipart/form-data"  >
                        @csrf
                        @method('put')
                        <div class="row">  
                            <div class="form-group  col-lg-6">
                                <label for="name">{{ __('levels.name') }}</label> <span class="text-danger">*</span>
                                <input id="name" type="text" name="name" data-parsley-trigger="change" placeholder="{{ __('levels.enter_name') }}" autocomplete="off" class="form-control" value="{{old('name',$zone->name)}}" required>
                                @error('name')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>
 
                            <div class="form-group col-lg-6">
                                <label for="status">{{ __('levels.status') }}</label> <span class="text-danger">*</span>
                                <select name="status" class="form-control @error('status') is-invalid @enderror">
                                    @foreach(trans('status') as $key => $status)
                                        <option value="{{ $key }}" {{ (old('status',$zone->status) == $key) ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div> 

                            <div class="form-group  col-lg-6">
                                <label for="position">{{ __('levels.position') }}</label>  
                                <input id="position" type="text" name="position" data-parsley-trigger="change" placeholder="{{ __('levels.position') }}" autocomplete="off" class="form-control" value="{{old('position',$zone->position)}}" >
                                @error('position')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>  

                        </div>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 ">
                                <button type="submit" class="btn btn-space btn-primary">{{ __('levels.update') }}</button>
                                <a href="{{ route('settings.zone.delivery-charge.index') }}" class="btn btn-space btn-secondary">{{ __('levels.cancel') }}</a>
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
 
