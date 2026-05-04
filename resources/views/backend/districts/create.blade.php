@extends('backend.partials.master')
@section('title')
    {{ __('district.title') }} {{ __('levels.add') }}
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
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">{{__('menus.user_role')}}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('departments.index') }}" class="breadcrumb-link">{{ __('district.title') }}</a></li>
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
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('district.create_district') }}</h4>
                </div>
                <div class="card-body">
                    <!-- <h2 class="pageheader-title">{{ __('district.create_district') }}</h2> -->
                    <form action="{{route('districts.store')}}"  method="POST">
                        @csrf
                        
                        <div class="form-group">
                            <label for="city_id">{{ __('district.city') }}</label> <span class="text-danger">*</span>
                            <select name="city_id" class="form-control select2 @error('city_id') is-invalid @enderror" id="city_id">
                                <option value="">{{ __('district.select_city') }}</option>
                                @foreach($cities as $key => $city)
                                    <option value="{{ $city->id }}" {{ (old('city_id') == $city->id) ? 'selected' : '' }}>{{ $city->name }}</option>
                                @endforeach
                            </select>
                            @error('city_id')
                            <small class="text-danger mt-2">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="name">{{ __('levels.name') }}</label> <span class="text-danger">*</span>
                            <input id="name" type="text" name="name" placeholder="{{ __('placeholder.Enter_name') }}" autocomplete="off" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" require>
                            @error('name')
                                <small class="text-danger mt-2">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 ">
                                <button type="submit" class="btn btn-space btn-primary">{{ __('levels.save') }}</button>
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
@endsection
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function() {
            $("#country_id").on('change', function() {
                var id = $(this).val();
                var op = " ";
                $.ajax({
                    type: "GET",
                    url: "{{ url('admin/country/by/city') }}/"+id,
                    success: function(data){
                        op +='<option  value="">--Select City--</option>';
                        for (var i=0; i<data.length; i++) {
                            op +='<option  value="'+data[i].id+'">'+data[i].name+'</option>';
                        }
                        $('#city_id').html(op);
                    }
                });
            })
        });
    </script>
@endpush

