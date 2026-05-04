<div class="row"> 
    <div class="form-group col-md-4">
        <label for="address">{{ __('levels.address') }}</label> <span class="text-danger">*</span>
        <input id="address" type="text" name="data[address]"  placeholder="Enter address" autocomplete="off" class="form-control @error('address') is-invalid @enderror" value="{{old('address',@$section['address'])}}" required>
        @error('address')
            <small class="text-danger mt-2">{{ $message }}</small>
        @enderror
    </div> 
    <div class="form-group col-md-4">
        <label for="phone">{{ __('levels.phone') }}</label> <span class="text-danger">*</span>
        <input id="phone" type="text" name="data[phone]"  placeholder="Enter phone" autocomplete="off" class="form-control @error('phone') is-invalid @enderror"  value="{{old('phone',@$section['phone'])}}" />
        @error('phone')
            <small class="text-danger mt-2">{{ $message }}</small>
        @enderror
    </div>  
    <div class="form-group col-md-4">
        <label for="email">{{ __('levels.email') }}</label> <span class="text-danger">*</span>
        <input id="email" type="text" name="data[email]"  placeholder="Enter email" autocomplete="off" class="form-control @error('email') is-invalid @enderror"  value="{{old('email',@$section['email'])}}" />
        @error('email')
            <small class="text-danger mt-2">{{ $message }}</small>
        @enderror
    </div>  
    <div class="form-group col-md-4">
        <label for="website">{{ __('levels.website') }}</label> <span class="text-danger">*</span>
        <input id="website" type="text" name="data[website]"  placeholder="Enter website" autocomplete="off" class="form-control @error('website') is-invalid @enderror"  value="{{old('website',@$section['website'])}}" />
        @error('website')
            <small class="text-danger mt-2">{{ $message }}</small>
        @enderror
    </div>  
  
</div>