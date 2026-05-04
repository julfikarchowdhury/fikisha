<h3 class="mt-3">{{ __('levels.play_store') }}</h3>
<div class="row">  
    <div class="form-group col-md-4">
        <label for="playstore_link">{{ __('levels.link') }}</label> <span class="text-danger">*</span>
        <input id="playstore_link" type="text" name="data[playstore_link]"  placeholder="{{ __('levels.Enter_middle_title') }}" autocomplete="off" class="form-control @error('playstore_link') is-invalid @enderror" value="{{old('playstore_link',@$section['playstore_link'])}}" required>
        @error('playstore_link')
            <small class="text-danger mt-2">{{ $message }}</small>
        @enderror
    </div>  
</div>

<h3 class="mt-3">{{ __('levels.ios_store') }}</h3>
<div class="row"> 
 
    <div class="form-group col-md-4">
        <label for="ios_link">{{ __('levels.link') }}</label> <span class="text-danger">*</span>
        <input id="ios_link" type="text" name="data[ios_link]"  placeholder="{{ __('levels.Enter_middle_title') }}" autocomplete="off" class="form-control @error('ios_link') is-invalid @enderror" value="{{old('ios_link',@$section['ios_link'])}}" required>
        @error('ios_link')
            <small class="text-danger mt-2">{{ $message }}</small>
        @enderror
    </div>  
</div>