@extends('backend.partials.master')
@section('title')
   {{ __('levels.service') }} {{ __('levels.add') }}
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
                            <li class="breadcrumb-item"><a href="{{ route('service.index') }}" class="breadcrumb-link">{{ __('levels.service') }}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{ __('levels.create') }}</a></li>
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
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('levels.service') }} {{ __('levels.add') }}</h4>
                </div>
                <div class="card-body">
                    <!-- <h2 class="pageheader-title">{{ __('levels.service') }} {{ __('levels.add') }}</h2> -->
                    <form action="{{route('service.store')}}"  method="POST" enctype="multipart/form-data" id="basicform">
                        @csrf
                        <div class="row">
                            
                            <div class="form-group col-md-6">
                                <label for="delivery_type_id">{{ __('levels.delivery_type') }}</label> <span class="text-danger">*</span>
                                <select id="delivery_type_id" name="delivery_type_id"  class="form-control @error('delivery_type_id') is-invalid @enderror" required>
                                    <option value="" selected>Select delivery type</option>
                                    <option value="1">Inside City</option>
                                    <option value="3">Outside City</option> 
                                </select>
                                @error('delivery_type_id')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="shipping_type_id">{{ __('levels.shipping_type') }}</label> <span class="text-danger">*</span>
                                <select id="shipping_type_id" name="shipping_type_id"  class="form-control @error('shipping_type_id') is-invalid @enderror" required>
                                    <option value="" selected>Select shipping type</option>  
                                </select>
                                @error('shipping_type_id')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label for="image">{{ __('levels.image') }} <span class="text-danger">*</span></label>
                                <input id="image" type="file" name="image" data-parsley-trigger="change" placeholder="Enter image" autocomplete="off" class="form-control @error('image') is-invalid @enderror" value="{{ old('image') }}" require>
                                @error('image')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div> 


                            <div class="form-group    col-md-6">
                                <label for="position">{{ __('levels.position') }}</label>
                                <input id="position" type="text" name="position" data-parsley-trigger="change" placeholder="{{ __('placeholder.Enter_Position') }}" autocomplete="off" class="form-control @error('position') is-invalid @enderror" value="{{old('position')}}" >
                                @error('position')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group  col-md-6">
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

                            <div class="form-group col-md-6"> 
                                <label for="summernote">{{ __('levels.description') }} <span class="text-danger">*</span></label>
                                <textarea  class="form-control  @error('description') is-invalid @enderror" name="description" id="summernote" rows="12"></textarea>
                                @error('description')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror 
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12  text-right">
                                <button type="submit" class="btn btn-space btn-primary">{{ __('levels.save') }}</button>
                                <a href="{{ route('service.index') }}" class="btn btn-space btn-secondary">{{ __('levels.cancel') }}</a>
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

 
            $('#delivery_type_id').change(function(){
             
                $.ajax({
                    type: "GET",
                    url: "{{ route('service.shippingtypes') }}",
                    dataType: "json",
                    data:{
                        delivery_type_id:$(this).val()
                    },
                    success: function (data) {
                       
                        $('#shipping_type_id').html(data.shipping_types);
                    },
                    error: function (error) {
                        $('#shipping_type_id').html('');
                    },
                });
            });

        });
    </script>
@endpush
