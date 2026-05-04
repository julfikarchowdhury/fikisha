 
<div class="row mt-3">
    @if(isset($option_id) && !blank($option_id))
    <input type="hidden" name="options[{{ $key }}][id]" value="{{ $option_id }}"/>
    @endif
    <div class="col-md-3">
        <label>{{ __('levels.from_km') }}</label>
        <input type="number" name="options[{{ $key }}][from_km]" step="any" 
            placeholder="{{ __('placeholder.Enter_from_km') }}" value="{{ $option->from_km }}" autocomplete="off"
            class="form-control" required>
        @error('from_km')
            <p class="text-danger">{{ $message }}</p>
        @enderror
    </div>
    <div class="col-md-3">
        <label>{{ __('levels.to_km') }}</label>
        <input type="number" name="options[{{ $key }}][to_km]" step="any" 
            placeholder="{{ __('placeholder.enter_to_km') }}" value="{{ $option->to_km }}" autocomplete="off"
            class="form-control" required>
        @error('to_km')
            <p class="text-danger">{{ $message }}</p>
        @enderror
    </div>
    <div class="col-md-3">
        <label>{{ __('parcel.basic_price') }}</label>
        <input type="number" step="any" name="options[{{ $key }}][basic_price]"
            placeholder="{{ __('placeholder.enter_basic_price') }}"
            value="{{ $option->basic_price }}"
            autocomplete="off" class="form-control" required>
        @error('basic_price')
            <p class="text-danger">{{ $message }}</p>
        @enderror
    </div>
    <div class="col-md-3">
        @if( $key == 1)
            <button class="btn btn-sm btn-primary mt-4" type="button" id="add-options"><i  class="fa fa-plus"></i></button>
        @else
            <i class="fa fa-times"></i>
        @endif
    </div>
</div>
