
<form action="{{ route('deliveryman.parcel.status.update') }}" method="post">
    @csrf
    <input type="hidden" value="{{ $request->parcel_id }}" name="parcel_id"  />
    <input type="hidden" value="{{ $request->status_action }}" name="status_action"  />
    <div class="modal-body">
        @if ($request->status_action == \App\Enums\ParcelStatus::PARTIAL_DELIVERED)
            <div class="form-group">
                <label for="cash_collection">{{ __('parcel.cash_collection') }} </label> <span class="text-danger">*</span>
                <div class="form-control-wrap">
                    <input type="text" class="form-control cash-collection" id="cash_collection" value="{{ old('cash_collection') }}" name="cash_collection" placeholder="Cash amount" required="">
                    @error('cash_collection')
                        <small class="text-danger mt-2">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        @endif
        <div class="form-group  ">
            <label for="note">{{ __('parcel.note')}}</label>
            <div class="form-control-wrap deliveryman-search">
                <textarea class="form-control" name="note" rows="5"></textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('levels.cancel') }} </button>
        <button type="submit" class="btn btn-primary">{{ __('levels.submit') }}</button>
    </div>
</form> 