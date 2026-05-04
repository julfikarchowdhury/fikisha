@extends('backend.partials.master')
@section('title')
    {{ __('parcel.holiday') }} {{ __('levels.add') }}
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
                            <li class="breadcrumb-item"><a href="{{ route('hrm.holiday.index') }}" class="breadcrumb-link">{{ __('parcel.holiday') }}</a></li>
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
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('parcel.holiday') }} {{ __('levels.create') }}</h4>
                </div>
                <div class="card-body">
                    <!-- <h2 class="pageheader-title">{{ __('parcel.holiday') }} {{ __('levels.create') }}</h2> -->
                    <form action="{{route('hrm.holiday.store')}}"  method="POST" enctype="multipart/form-data" id="basicform">
                        @csrf
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="title">{{ __('asset.name') }}</label> <span class="text-danger">*</span>
                                    <input id="title" type="text" name="name" data-parsley-trigger="change" placeholder="{{ __('placeholder.Enter_name') }}" autocomplete="off" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" require>
                                    @error('name')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div> 
                            </div> 
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="from_date">{{ __('parcel.from_date') }}</label> <span class="text-danger">*</span>
                                    <input id="from_date" type="date" name="from_date" data-parsley-trigger="change"  autocomplete="off" class="form-control @error('from_date') is-invalid @enderror" value="{{ old('from_date') }}" >
                                    @error('from_date')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div> 
                            </div> 
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="to_date">{{ __('parcel.to_date') }}</label> <span class="text-danger">*</span>
                                    <input id="to_date" type="date" name="to_date" data-parsley-trigger="change"  autocomplete="off" class="form-control @error('to_date') is-invalid @enderror" value="{{ old('to_date') }}" >
                                    @error('to_date')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div> 
                            </div> 
                            <div class="col-12 col-md-6">
                                <div class="form-group">
                                    <label for="note">{{ __('parcel.note') }}</label> 
                                    <textarea id="note"  name="note" class="form-control @error('note') is-invalid @enderror"  >{{ old('note') }}</textarea>
                                    @error('note')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div> 
                            </div> 
                        </div>

                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 ">
                                <button type="submit" class="btn btn-space btn-primary">{{ __('levels.save') }}</button>
                                <a href="{{ route('hrm.holiday.index') }}" class="btn btn-space btn-secondary">{{ __('levels.cancel') }}</a>
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


