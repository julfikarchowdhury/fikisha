@extends('backend.partials.master')
@section('title')
    {{ __('levels.add_multiple') }} {{ __('city.title') }}
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
                            <li class="breadcrumb-item"><a href="{{ route('departments.index') }}" class="breadcrumb-link">{{ __('city.title') }}</a></li>
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
        <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('city.create_city') }}</h4>
                </div>
                <div class="card-body">
                    <!-- <h2 class="pageheader-title">{{ __('city.create_city') }}</h2> -->
                    <form action="{{ route('multiple_city.store')}}"  method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="province_id">{{ __('parcel.province') }}</label> <span class="text-danger">*</span>
                                    <select name="province_id" class="form-control select2 @error('province_id') is-invalid @enderror">
                                        <option value="">{{ __('levels.select') }} {{ __('parcel.province') }}</option>
                                        @foreach($provinces as $key => $province)
                                            <option value="{{ $province->id }}" {{ (old('province_id') == $province->id) ? 'selected' : '' }}>{{ $province->name }}({{ $province->province_code }})</option>
                                        @endforeach
                                    </select>
                                    @error('province_id')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="province_code">{{ __('levels.province_code') }}</label> <span class="text-danger">*</span>
                                    <input id="province_code" type="text" name="province_code" placeholder="{{ __('levels.province_code') }}" autocomplete="off" class="form-control @error('name') is-invalid @enderror" value="{{ old('province_code') }}" required>
                                    @error('province_code')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <table id="mainData" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">{{ __('levels.name') }}<span class="text-danger">*</span></th>
                                            <th scope="col">{{ __('parcel.portal_code') }}<span class="text-danger">*</span></th>
                                            <th scope="col" class="text-center">
                                                <button type="button" class="btn btn-success" onclick="addRow()">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="item-box">
                                        <tr>
                                            <td><input type="text" name="name[]" placeholder="{{ __('placeholder.Enter_name') }}" autocomplete="off" class="form-control" required></td>
                                            <td><input type="number" name="portal_code[]" placeholder="{{ __('parcel.portal_code') }}" autocomplete="off" class="form-control" required></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm remove">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row mt-3">
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
        $(document).ready(function(){
            $('.select2').select2();
        });

        function addRow(){
            var tr = '<tr>'+
                    '<td><input type="text" name="name[]" placeholder="{{ __('placeholder.Enter_name') }}" autocomplete="off" class="form-control" required></td>'+
                    '<td><input type="number" name="portal_code[]" placeholder="{{ __('parcel.portal_code') }}" autocomplete="off" class="form-control" required></td>'+
                    '<td class="text-center">'+
                        '<button type="button" class="btn btn-danger btn-sm remove">'+
                            '<i class="fa fa-trash"></i>'+
                        '</button>'+
                    '</td>'+
                '</tr>';
            $('tbody').append(tr);
        }

        $(document).on("click", ".remove", function () {
            var length = $("#mainData tbody tr").length;
            if (length == 1) {
                alert('You can not remove last one');
            } else {
                $(this).parent().parent().remove();
            }
        });
    </script>
@endpush

