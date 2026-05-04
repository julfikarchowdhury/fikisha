@extends('backend.partials.master')
@section('title')
    {{ __('country.title') }} {{ __('levels.edit') }}
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
                            <li class="breadcrumb-item"><a href="{{route('dashboard.index')}}"
                                    class="breadcrumb-link">{{ __('levels.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link">{{ __('city.title')
                                    }}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{ __('levels.edit')
                                    }}</a></li>
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
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('city.edit_city') }}</h4>
                </div>
                <div class="card-body">
                    <!-- <h2 class="pageheader-title">{{ __('city.edit_city') }}</h2> -->
                    <form action="{{ route('cities.update',$city->id) }}"  method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="province_id">{{ __('parcel.province') }}</label> <span class="text-danger">*</span>
                            <select name="province_id" class="form-control select2 @error('province_id') is-invalid @enderror">
                                <option value="">{{ __('levels.select') }} {{ __('parcel.province') }}</option>
                                @foreach($provinces as $key => $province)
                                    <option value="{{ $province->id }}" {{ (old('province_id', $city->province_id) == $province->id) ? 'selected' : '' }}>{{ $province->name }}({{ $province->province_code }})</option>
                                @endforeach
                            </select>
                            @error('province_id')
                            <small class="text-danger mt-2">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="name">{{ __('levels.name') }}</label> <span class="text-danger">*</span>
                            <input id="name" type="text" name="name" placeholder="{{ __('placeholder.Enter_name') }}" autocomplete="off" class="form-control @error('name') is-invalid @enderror" value="{{old('name',$city->name)}}" require>
                            @error('name')
                                <small class="text-danger mt-2">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="portal_code">{{ __('parcel.portal_code') }} <span class="text-danger">*</span></label>
                            <input id="portal_code" type="text" name="portal_code" placeholder="{{ __('parcel.portal_code') }}" autocomplete="off" class="form-control @error('portal_code') is-invalid @enderror" value="{{ old('portal_code',$city->portal_code) }}" required>
                            @error('portal_code')
                                <small class="text-danger mt-2">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 ">
                                <button type="submit" class="btn btn-space btn-primary">{{ __('levels.update') }}</button>
                                <a href="{{ route('cities.index') }}" class="btn btn-space btn-secondary">{{ __('levels.cancel') }}</a>
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


