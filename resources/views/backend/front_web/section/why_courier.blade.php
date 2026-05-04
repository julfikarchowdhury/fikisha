<div class="row"> 
    <div class="col-md-4">
        <div class="form-group ">
            <label for="title">{{ __('levels.title') }}</label> <span class="text-danger">*</span>
            <input id="title" type="text" name="data[title]"  placeholder="{{ __('levels.Enter_title') }}" autocomplete="off" class="form-control @error('title') is-invalid @enderror" value="{{old('title',@$section['title'])}}" required>
            @error('title')
                <small class="text-danger mt-2">{{ $message }}</small>
            @enderror
        </div>  
        <div class="form-group ">
            <label for="banner">{{ __('levels.banner') }}</label> <span class="text-danger">*</span>
            <input id="banner" type="file" name="data[banner]"  class="form-control @error('banner') is-invalid @enderror">
            @error('banner')
                <small class="text-danger mt-2">{{ $message }}</small>
            @enderror
            
        </div>    
    </div>
    <div class="col-md-4">
        <div  >
            <img src="{{ @$section['banner'] }}" width="50%" />
        </div>
    </div>
</div>