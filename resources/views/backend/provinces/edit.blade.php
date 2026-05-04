@extends('backend.partials.master')
@section('title')
    {{ __('levels.province') }} {{ __('levels.edit') }}
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
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link">{{ __('levels.province') }}</a></li>
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
        <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('levels.edit') }} {{ __('levels.province') }}</h4>
                </div>
                <div class="card-body">
                    <!-- <h2 class="pageheader-title">{{ __('levels.edit') }} {{ __('levels.province') }}</h2> -->
                    <form action="{{ route('province.update',['id'=>$province->id]) }}"  method="POST">
                        @csrf
                        @method('PUT')
                       
                        <div class="form-group">
                            <label for="name">{{ __('levels.name') }}</label> <span class="text-danger">*</span>
                            <input id="name" type="text" name="name" placeholder="{{ __('placeholder.Enter_name') }}" autocomplete="off" class="form-control @error('name') is-invalid @enderror" value="{{old('name',$province->name)}}" require>
                            @error('name')
                                <small class="text-danger mt-2">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="province_code">{{ __('levels.province_code') }} <span class="text-danger">*</span></label>
                            <input id="province_code" type="text" name="province_code" placeholder="{{ __('levels.Enter_province_code') }}" autocomplete="off" class="form-control @error('province_code') is-invalid @enderror" value="{{ old('province_code',$province->province_code) }}" >
                            @error('province_code')
                                <small class="text-danger mt-2">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="position">{{ __('levels.position') }}  </label>
                            <input id="position" type="text" name="position" placeholder="{{ __('levels.enter_position') }}" autocomplete="off" class="form-control @error('position') is-invalid @enderror" value="{{ old('position',$province->position) }}" >
                            @error('position')
                                <small class="text-danger mt-2">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="description">{{ __('levels.description') }}  </label>
                            <textarea id="description"  name="description" placeholder="{{ __('placeholder.Enter_description') }}"  class="form-control @error('description') is-invalid @enderror" >{{ old('description',$province->description) }}</textarea>
                            @error('description')
                                <small class="text-danger mt-2">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 ">
                                <button type="submit" class="btn btn-space btn-primary">{{ __('levels.update') }}</button>
                                <a href="{{ route('province.index') }}" class="btn btn-space btn-secondary">{{ __('levels.cancel') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- end basic form -->
    </div>
</div>
<!-- end wrapper  -->
@endsection()
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endpush


