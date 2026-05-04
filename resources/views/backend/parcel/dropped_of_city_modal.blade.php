<div class="modal fade" id="parcelstatus{{ \App\Enums\ParcelStatus::DROP_OFF_CITY }}"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content" id="data-modal">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">{{ __('parcelStatus.'.\App\Enums\ParcelStatus::DROP_OFF_CITY) }}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{ route('parcel.dynamic.status.update', ['status'=>\App\Enums\ParcelStatus::DROP_OFF_CITY,'page'=>$request->page,'filter'=>$request->filter? $request->filter:'']) }}" method="post">
            @csrf
            <input type="hidden" value="" name="parcel_id" id="modal_parcel_id" class="modal_parcel_id"/> 
            <div class="modal-body">
                <div class="form-group  ">
                    <label for="note">{{ __('parcel.note')}}</label>
                    <div class="form-control-wrap deliveryman-search">
                       <textarea class="form-control" name="note" rows="5"></textarea>
                    </div>
                </div>
           
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('levels.cancel') }}</button>
              <button type="submit" class="btn btn-primary">{{ __('levels.save') }}</button>
            </div>
        </form>

    </div>
</div>
</div>
