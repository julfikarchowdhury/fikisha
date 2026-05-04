@extends('backend.partials.master')
@section('title')
    {{ __('vehicle.title') }} {{ __('levels.add') }}
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
                            <li class="breadcrumb-item"><a href="{{ route('vehicles.index') }}" class="breadcrumb-link">{{ __('vehicle.title') }}</a></li>
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
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('vehicle.vehicle_add') }}</h4>
                </div>
                <div class="card-body">
                    <!-- <h2 class="pageheader-title">{{ __('vehicle.vehicle_add') }}</h2> -->
                    <form action="{{route('vehicles.store')}}"  method="POST" enctype="multipart/form-data" id="basicform">
                        @csrf
                        <div class="col-6">
                            <div class="row">
                                <div class="col-12 col-md-12">
                                    <div class="form-group">
                                        <label for="title">{{ __('levels.name') }}</label> <span class="text-danger">*</span>
                                        <input id="title" type="text" name="name" data-parsley-trigger="change" placeholder="{{ __('placeholder.Enter_name') }}" autocomplete="off" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" require>
                                        @error('name')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="hub_id">{{ __('parcel.hub') }} <span class="text-danger">*</span></label>
                                        <select style="width: 100%" id="hub_id" class="form-control select2" name="hub_id">
                                            <option value="">{{ __('menus.select') }} {{ __('parcel.hub') }}</option>
                                            @foreach (hubs() as $hub)
                                                <option value="{{ $hub->id }}" @selected(old('hub_id') == $hub->id)>{{ $hub->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('hub_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="description">{{ __('levels.description') }}</label>
                                        <textarea name="description" id="description" class="form-control" rows="5" placeholder="{{ __('placeholder.Enter_description') }}" autocomplete="off"></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="registration_number">{{ __('vehicle.registration_number') }} <span class="text-danger">*</span></label>
                                        <input id="registration_number" type="text" name="registration_number" placeholder="{{ __('vehicle.enter_registration_number') }}" autocomplete="off" class="form-control @error('registration_number') is-invalid @enderror" value="{{ old('registration_number') }}">
                                        @error('registration_number')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="capacity">{{ __('vehicle.capacity') }} <span class="text-danger">*</span></label>
                                        <input id="capacity" type="text" name="capacity" placeholder="{{ __('vehicle.enter_capacity') }}" autocomplete="off" class="form-control @error('capacity') is-invalid @enderror" value="{{ old('capacity') }}">
                                        @error('capacity')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="size">{{ __('vehicle.size') }} <span class="text-danger">*</span></label>
                                        <input id="size" type="text" name="size" placeholder="{{ __('vehicle.enter_size') }}" autocomplete="off" class="form-control @error('size') is-invalid @enderror" value="{{ old('size') }}">
                                        @error('size')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="status">{{ __('levels.status') }}</label> <span class="text-danger">*</span>
                                        <select name="status" class="form-control @error('status') is-invalid @enderror">
                                            @foreach (trans('status') as $key => $status)
                                                <option value="{{ $key }}" {{ old('status', \App\Enums\Status::ACTIVE) == $key ? 'selected' : '' }}>
                                                    {{ $status }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('status')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <button type="submit" class="btn btn-space btn-primary">{{ __('levels.save') }}</button>
                                    <a href="{{ route('vehicles.index') }}" class="btn btn-space btn-secondary">{{ __('levels.cancel') }}</a>
                                </div>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css" />
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $(".select2").select2();
        });
    </script>
@endpush

