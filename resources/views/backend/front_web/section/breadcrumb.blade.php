<div class="row"> 
 
    <div class="form-group col-md-4">
        <label for="banner">{{ __('levels.banner') }}</label> <span class="text-danger">*</span>
        <input id="banner" type="file" name="data[banner]"  class="form-control @error('banner') is-invalid @enderror">
        @error('banner')
            <small class="text-danger mt-2">{{ $message }}</small>
        @enderror
        <div class="mt-3">
            <img src="{{ @$section['banner'] }}" width="30%" />
        </div>
    </div>    
</div>