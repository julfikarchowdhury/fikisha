<div class="modal fade" id="parcelstatus{{ \App\Enums\ParcelStatus::PARCEL_CANCEL }}" role="dialog"
    aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="data-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    {{ __('parcelStatus.' . \App\Enums\ParcelStatus::PARCEL_CANCEL) }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('parcel.parcel_cancel', ['page' => $request->page, 'filter' => $request->filter ? $request->filter : '']) }}" method="post">
                @csrf
                <input type="hidden" value="" name="parcel_id" id="modal_parcel_id" class="modal_parcel_id" />
                <div class="modal-body">
                    <div class="form-group">
                        <label>Who {{ __('parcelStatus.' . \App\Enums\ParcelStatus::PARCEL_CANCEL) }}</label> <span class="text-danger">*</span>
                        <select class="form-control select2" name="who" style="width: 100%;" required>
                            <option value="Recipient">Recipient</option>
                            <option value="Sender">Sender</option>
                            <option value="Dispatcher">Dispatcher</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="note">Reason</label>
                        <div class="form-control-wrap deliveryman-search">
                            <textarea class="form-control" name="note" rows="5"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">{{ __('levels.cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('levels.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
