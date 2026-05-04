{{-- READY_TO_REASSIGN_REGULAR --}}
<div class="modal fade" id="parcelstatus{{ \App\Enums\ParcelStatus::READY_TO_REASSIGN_REGULAR }}" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="data-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans("parcelStatusShow." . \App\Enums\ParcelStatus::READY_TO_REASSIGN_REGULAR) }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('parcel.ready_to_reassign',['page'=>$request->page,'filter'=>$request->filter? $request->filter:'']) }}" method="post">
                @csrf
                <input type="hidden" class="modal_parcel_id" id="modal_parcel_id" value="" name="parcel_id" />
                <div class="modal-body">
                    <div class="form-group">
                        <label for="note">{{ __('parcel.note')}}</label>
                        <div class="form-control-wrap deliveryman-search">
                            <textarea class="form-control" name="note" rows="5"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="custom-control custom-checkbox">
                                    <input id="send_sms_merchant" name="send_sms_merchant" class="custom-control-input" type="checkbox"><span class="custom-control-label">Send SMS for merchant </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('levels.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">{{ __('levels.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- READY_TO_REASSIGN_BOOKING --}}
<div class="modal fade" id="parcelstatus{{ \App\Enums\ParcelStatus::READY_TO_REASSIGN_BOOKING }}" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="data-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans("parcelStatusShow." . \App\Enums\ParcelStatus::READY_TO_REASSIGN_BOOKING) }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('parcel.ready_to_reassign_booking',['page'=>$request->page,'filter'=>$request->filter? $request->filter:'']) }}" method="post">
                @csrf
                <input type="hidden" class="modal_parcel_id" id="modal_parcel_id" value="" name="parcel_id" />
                <div class="modal-body">
                    <div class="form-group">
                        <label for="note">{{ __('parcel.note')}}</label>
                        <div class="form-control-wrap deliveryman-search">
                            <textarea class="form-control" name="note" rows="5"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="custom-control custom-checkbox">
                                    <input id="send_sms_merchant" name="send_sms_merchant" class="custom-control-input" type="checkbox"><span class="custom-control-label">Send SMS for merchant </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('levels.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">{{ __('levels.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- CONFIRMED_BOOKING --}}
<div class="modal fade" id="parcelstatus{{ \App\Enums\ParcelStatus::CONFIRMED_BOOKING }}" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="data-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans("parcelStatusShow." . \App\Enums\ParcelStatus::CONFIRMED_BOOKING) }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('parcel.confirmed_booking',['page'=>$request->page,'filter'=>$request->filter? $request->filter:'']) }}" method="post">
                @csrf
                <input type="hidden" class="modal_parcel_id" id="modal_parcel_id" value="" name="parcel_id" />
                <div class="modal-body">
                    <div class="form-group">
                        <label for="note">{{ __('parcel.note')}}</label>
                        <div class="form-control-wrap deliveryman-search">
                            <textarea class="form-control" name="note" rows="5"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="custom-control custom-checkbox">
                                    <input id="send_sms_merchant" name="send_sms_merchant" class="custom-control-input" type="checkbox"><span class="custom-control-label">Send SMS for merchant </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('levels.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">{{ __('levels.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- CONFIRMED_BOOKING --}}
<div class="modal fade" id="parcelstatus{{ \App\Enums\ParcelStatus::CONFIRMED_BOOKING }}" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="data-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans("parcelStatusShow." . \App\Enums\ParcelStatus::CONFIRMED_BOOKING) }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('parcel.confirmed_booking',['page'=>$request->page,'filter'=>$request->filter? $request->filter:'']) }}" method="post">
                @csrf
                <input type="hidden" class="modal_parcel_id" id="modal_parcel_id" value="" name="parcel_id" />
                <div class="modal-body">
                    <div class="form-group">
                        <label for="note">{{ __('parcel.note')}}</label>
                        <div class="form-control-wrap deliveryman-search">
                            <textarea class="form-control" name="note" rows="5"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="custom-control custom-checkbox">
                                    <input id="send_sms_merchant" name="send_sms_merchant" class="custom-control-input" type="checkbox"><span class="custom-control-label">Send SMS for merchant </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('levels.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">{{ __('levels.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- PROCESSING --}}
<div class="modal fade" id="parcelstatus{{ \App\Enums\ParcelStatus::PROCESSING }}" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="data-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ trans("parcelStatusShow." . \App\Enums\ParcelStatus::PROCESSING) }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('parcel.order_processing',['page'=>$request->page,'filter'=>$request->filter? $request->filter:'']) }}" method="post">
                @csrf
                <input type="hidden" class="modal_parcel_id" id="modal_parcel_id" value="" name="parcel_id" />
                <div class="modal-body">
                    <div class="form-group">
                        <label for="note">{{ __('parcel.note')}}</label>
                        <div class="form-control-wrap deliveryman-search">
                            <textarea class="form-control" name="note" rows="5"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="custom-control custom-checkbox">
                                    <input id="send_sms_merchant" name="send_sms_merchant" class="custom-control-input" type="checkbox"><span class="custom-control-label">Send SMS for merchant </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('levels.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">{{ __('levels.save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- DELIVERY_FAILURE --}}
<div class="modal fade" id="parcelstatus{{ \App\Enums\ParcelStatus::DELIVERY_FAILURE }}" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="data-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    {{ __('parcelStatus.' . \App\Enums\ParcelStatus::DELIVERY_FAILURE) }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('parcel.dynamic.status.update', ['status' => \App\Enums\ParcelStatus::DELIVERY_FAILURE, 'page' => $request->page, 'filter' => $request->filter ? $request->filter : '']) }}" method="post">
                @csrf
                <input type="hidden" value="" name="parcel_id" id="modal_parcel_id" class="modal_parcel_id" />
                <div class="modal-body">
                    <div class="form-group">
                        <label for="note">{{ __('parcel.note') }}</label>
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

{{-- DELIVERY_FAILED --}}
<div class="modal fade" id="parcelstatus{{ \App\Enums\ParcelStatus::DELIVERY_FAILED }}" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="data-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    {{ __('parcelStatus.' . \App\Enums\ParcelStatus::DELIVERY_FAILED) }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('parcel.dynamic.status.update', ['status' => \App\Enums\ParcelStatus::DELIVERY_FAILED, 'page' => $request->page, 'filter' => $request->filter ? $request->filter : '']) }}" method="post">
                @csrf
                <input type="hidden" value="" name="parcel_id" id="modal_parcel_id" class="modal_parcel_id" />
                <div class="modal-body">
                    <div class="form-group">
                        <label>Who {{ __('parcelStatus.' . \App\Enums\ParcelStatus::DELIVERY_FAILED) }}</label> <span class="text-danger">*</span>
                        <select class="form-control select2" name="who" style="width: 100%;" required>
                            <option value="Delivery man">Delivery man</option>
                            <option value="Recipient">Recipient</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="note">Reason of Failure</label>
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

{{-- UNCONFIRMED_BOOKING --}}
<div class="modal fade" id="parcelstatus{{ \App\Enums\ParcelStatus::UNCONFIRMED_BOOKING }}" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="data-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    {{ __('parcelStatus.' . \App\Enums\ParcelStatus::UNCONFIRMED_BOOKING) }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('parcel.dynamic.status.update', ['status' => \App\Enums\ParcelStatus::UNCONFIRMED_BOOKING, 'page' => $request->page, 'filter' => $request->filter ? $request->filter : '']) }}" method="post">
                @csrf
                <input type="hidden" value="" name="parcel_id" id="modal_parcel_id" class="modal_parcel_id" />
                <div class="modal-body">
                    <div class="form-group">
                        <label for="note">{{ __('parcel.note') }}</label>
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

{{-- DROPPED_OFF_HUB2 --}}
<div class="modal fade" id="parcelstatus{{ \App\Enums\ParcelStatus::DROPPED_OFF_HUB2 }}" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="data-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    {{ __('parcelStatus.' . \App\Enums\ParcelStatus::DROPPED_OFF_HUB2) }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('parcel.dynamic.status.update', ['status' => \App\Enums\ParcelStatus::DROPPED_OFF_HUB2, 'page' => $request->page, 'filter' => $request->filter ? $request->filter : '']) }}" method="post">
                @csrf
                <input type="hidden" value="" name="parcel_id" id="modal_parcel_id" class="modal_parcel_id" />
                <div class="modal-body">
                    <div class="form-group">
                        <label for="note">{{ __('parcel.note') }}</label>
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

{{-- HEADING_TO_DROP_OFF --}}
<div class="modal fade" id="parcelstatus{{ \App\Enums\ParcelStatus::HEADING_TO_DROP_OFF }}" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="data-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    {{ __('parcelStatus.' . \App\Enums\ParcelStatus::HEADING_TO_DROP_OFF) }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('parcel.dynamic.status.update', ['status' => \App\Enums\ParcelStatus::HEADING_TO_DROP_OFF, 'page' => $request->page, 'filter' => $request->filter ? $request->filter : '']) }}" method="post">
                @csrf
                <input type="hidden" value="" name="parcel_id" id="modal_parcel_id" class="modal_parcel_id" />
                <div class="modal-body">
                    <div class="form-group">
                        <label for="note">{{ __('parcel.note') }}</label>
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

{{-- RETURNED_MERCHANT --}}
<div class="modal fade" id="parcelstatus{{ \App\Enums\ParcelStatus::RETURNED_MERCHANT }}" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="data-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    {{ __('parcelStatus.' . \App\Enums\ParcelStatus::RETURNED_MERCHANT) }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('parcel.dynamic.status.update', ['status' => \App\Enums\ParcelStatus::RETURNED_MERCHANT, 'page' => $request->page, 'filter' => $request->filter ? $request->filter : '']) }}" method="post">
                @csrf
                <input type="hidden" value="" name="parcel_id" id="modal_parcel_id" class="modal_parcel_id" />
                <div class="modal-body">
                    <div class="form-group">
                        <label for="note">Reason of Return</label>
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

{{-- DELIVERY_MAN_ASSIGN_SECOND_PROVINCE --}}
<div class="modal fade" id="parcelstatus{{ \App\Enums\ParcelStatus::DELIVERY_MAN_ASSIGN_SECOND_PROVINCE }}" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="data-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    {{ __('parcelStatus.' . \App\Enums\ParcelStatus::DELIVERY_MAN_ASSIGN_SECOND_PROVINCE) }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('parcel.dynamic.status.update', ['status' => \App\Enums\ParcelStatus::DELIVERY_MAN_ASSIGN_SECOND_PROVINCE, 'page' => $request->page, 'filter' => $request->filter ? $request->filter : '']) }}" method="post">
                @csrf
                <input type="hidden" value="" name="parcel_id" id="modal_parcel_id" class="modal_parcel_id" />
                <div class="modal-body">
                    <div class="form-group">
                        <label for="delivery_man_second_province">{{ __('deliveryman.title')}}</label> <span class="text-danger">*</span>
                        <div class="form-control-wrap deliveryman-search">
                            <select id="delivery_man_second_province" class="form-control delivery_man_second_province" name="delivery_man_id" data-province_id="" data-url="{{ route('parcel.deliveryman.search') }}" >
                                <option value="">Select delivery man</option>
                                @foreach ($deliverymans as $deliveryman)
                                    <option value="{{ $deliveryman->id }}">{{ $deliveryman->user->name }}</option>
                                @endforeach
                            </select>
                            @error('delivery_man_id')
                                <small class="text-danger mt-2">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="note">{{ __('parcel.note') }}</label>
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