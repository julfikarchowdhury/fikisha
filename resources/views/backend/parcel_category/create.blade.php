@extends('backend.partials.master')
@section('title')
    {{ __('parcelcategory.parcel_category') }} {{ __('levels.add') }}
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
                            <li class="breadcrumb-item"><a href="{{ route('parcel.category.index') }}" class="breadcrumb-link">{{ __('parcelcategory.parcel_category') }}</a></li>
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
        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('parcelcategory.parcel_category') }} {{ __('levels.add') }}</h4>
                </div>
                <div class="card-body">
                    <!-- <h2 class="pageheader-title">{{ __('parcelcategory.parcel_category') }} {{ __('levels.add') }}</h2> -->
                    <form action="{{route('parcel.category.store')}}"  method="POST" enctype="multipart/form-data" id="basicform">
                        @csrf
                        <div class="form-group">
                            <label for="name">{{ __('levels.name') }}</label> <span class="text-danger">*</span>
                            <input id="name" type="text" name="name" data-parsley-trigger="change" placeholder="{{ __('placeholder.Enter_name') }}" autocomplete="off" class="form-control @error('title') is-invalid @enderror" value="{{old('title')}}" require>
                            @error('name')
                                <small class="text-danger mt-2">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="form-group  ">
                            <label for="position">{{ __('levels.position') }}</label>
                            <input id="position" type="number" name="position" data-parsley-trigger="change" placeholder="{{ __('placeholder.Enter_Position') }}" autocomplete="off" class="form-control" value="{{old('position')}}" >
                            @error('position')
                                <small class="text-danger mt-2">{{ $message }}</small>
                            @enderror
                        </div>
 
                        <div class="form-group">
                            <label for="status">{{ __('levels.status') }}</label> <span class="text-danger">*</span>
                            <select name="status" class="form-control @error('status') is-invalid @enderror">
                                @foreach(trans('status') as $key => $status)
                                    <option value="{{ $key }}" {{ (old('status',\App\Enums\Status::ACTIVE) == $key) ? 'selected' : '' }}>{{ $status }}</option>
                                @endforeach
                            </select>
                            @error('status')
                                <small class="text-danger mt-2">{{ $message }}</small>
                            @enderror
                        </div>
 
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 ">
                                <button type="submit" class="btn btn-space btn-primary">{{ __('levels.save') }}</button>
                                <a href="{{ route('parcel.category.index') }}" class="btn btn-space btn-secondary">{{ __('levels.cancel') }}</a>
                            </div>
                        </div>
 
                    </form>
                </div>
            </div>
        </div>
        <!-- end basic form -->
    </div>
</div>
@endsection

