@extends('backend.partials.master')
@section('title')
   {{ __('levels.slider') }} {{ __('levels.edit') }}
@endsection
@section('maincontent')
<div class="container-fluid  dashboard-content">
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="page-header">
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb"> 
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">{{__('levels.front_web')}}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('service.index') }}" class="breadcrumb-link">{{ __('levels.slider') }}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{ __('levels.edit') }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="  col-sm-12 col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('levels.slider') }} {{ __('levels.edit') }}</h4>
                </div>
                <div class="card-body">
                    <!-- <h2 class="pageheader-title">{{ __('levels.slider') }} {{ __('levels.edit') }}</h2> -->
                    <form action="{{ route('slider.update', ['id' => $singleSlider->id, 'update' => 'true']) }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        <div class="row">
                            
                            <div class="form-group    col-md-6">
                                <label for="title">{{ __('levels.title') }}</label>
                                <input id="title" type="text" name="title" data-parsley-trigger="change" placeholder="{{ __('placeholder.Enter_title') }}" class="form-control @error('title') is-invalid @enderror" value="{{old('title',$singleSlider->title)}}" >
                                @error('title')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>
 
                            <div class="form-group    col-md-6">
                                <label for="small_title">{{ __('levels.small_title') }}</label>
                                <input id="small_title" type="text" name="small_title" data-parsley-trigger="change" placeholder="{{ __('placeholder.Enter_small_title') }}" class="form-control @error('small_title') is-invalid @enderror" value="{{old('small_title',$singleSlider->small_title)}}" >
                                @error('small_title')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="slider">{{ __('levels.slider') }} <span class="text-danger">*</span></label>
                                <input id="slider" type="file" name="slider" data-parsley-trigger="change" placeholder="Enter slider" autocomplete="off" class="form-control @error('slider') is-invalid @enderror" value="{{ old('slider') }}" require>
                                @error('slider')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            
                            </div> 
  
                            <div class="form-group    col-md-6">
                                <label for="position">{{ __('levels.position') }}</label>
                                <input id="position" type="text" name="position" data-parsley-trigger="change" placeholder="{{ __('placeholder.Enter_Position') }}" autocomplete="off" class="form-control @error('position') is-invalid @enderror" value="{{old('position',$singleSlider->position)}}" >
                                @error('position')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group  col-md-6">
                                <label for="status">{{ __('levels.status') }}</label> <span class="text-danger">*</span>
                                <select name="status" class="form-control @error('status') is-invalid @enderror">
                                    @foreach(trans('status') as $key => $status)
                                        <option value="{{ $key }}" {{ (old('status',$singleSlider->status) == $key) ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>
  
                        </div>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12  text-right">
                                <button type="submit" class="btn btn-space btn-primary">{{ __('levels.save') }}</button>
                                <a href="{{ route('slider.index') }}" class="btn btn-space btn-secondary">{{ __('levels.cancel') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles') 
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">

@endpush

@push('scripts') 
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script> 
    <script>
        $(document).ready(function() {
            $('#summernote').summernote({
                placeholder: '{{ __("placeholder.Enter_description")}}' ,
                height: 182
            });
 
        });
    </script>
@endpush
