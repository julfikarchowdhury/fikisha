{{-- DELIVERY_ATTEMPT1 --}}
<div class="modal fade" id="parcelstatus{{ \App\Enums\ParcelStatus::DELIVERY_ATTEMPT1 }}" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="data-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('parcelStatus.'.\App\Enums\ParcelStatus::DELIVERY_ATTEMPT1) }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('parcel.dynamic.status.update', ['status'=>\App\Enums\ParcelStatus::DELIVERY_ATTEMPT1,'page'=>$request->page,'filter'=>$request->filter? $request->filter:'']) }}" method="post">
                @csrf
                <input type="hidden" value="" name="parcel_id" id="modal_parcel_id" class="modal_parcel_id" />
                <div class="modal-body">
                    <div class="form-group">
                        <label for="note">{{ __('parcel.note')}}</label>
                        <div class="form-control-wrap deliveryman-search">
                            <textarea class="form-control" name="note" rows="5" id="note"></textarea>
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

{{-- DELIVERY_ATTEMPT2 --}}
<div class="modal fade" id="parcelstatus{{ \App\Enums\ParcelStatus::DELIVERY_ATTEMPT2 }}" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="data-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('parcelStatus.'.\App\Enums\ParcelStatus::DELIVERY_ATTEMPT2) }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('parcel.dynamic.status.update', ['status'=>\App\Enums\ParcelStatus::DELIVERY_ATTEMPT2,'page'=>$request->page,'filter'=>$request->filter? $request->filter:'']) }}" method="post">
                @csrf
                <input type="hidden" value="" name="parcel_id" id="modal_parcel_id" class="modal_parcel_id" />
                <div class="modal-body">
                    <div class="form-group">
                        <label for="note">{{ __('parcel.note')}}</label>
                        <div class="form-control-wrap deliveryman-search">
                            <textarea class="form-control" name="note" rows="5" id="note"></textarea>
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

{{-- DELIVERY_ATTEMPT3 --}}
<div class="modal fade" id="parcelstatus{{ \App\Enums\ParcelStatus::DELIVERY_ATTEMPT3 }}" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="data-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('parcelStatus.'.\App\Enums\ParcelStatus::DELIVERY_ATTEMPT3) }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('parcel.dynamic.status.update', ['status'=>\App\Enums\ParcelStatus::DELIVERY_ATTEMPT3,'page'=>$request->page,'filter'=>$request->filter? $request->filter:'']) }}" method="post">
                @csrf
                <input type="hidden" value="" name="parcel_id" id="modal_parcel_id" class="modal_parcel_id" />
                <div class="modal-body">
                    <div class="form-group">
                        <label for="note">{{ __('parcel.note')}}</label>
                        <div class="form-control-wrap deliveryman-search">
                            <textarea class="form-control" name="note" rows="5" id="note"></textarea>
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

{{-- DELIVERY_MAN_ASSIGN1 --}}
<div class="modal fade" id="parcelstatus{{ \App\Enums\ParcelStatus::DELIVERY_MAN_ASSIGN1 }}" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="data-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('parcelStatus.'.\App\Enums\ParcelStatus::DELIVERY_MAN_ASSIGN1) }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('parcel.dynamic.status.update', ['status'=>\App\Enums\ParcelStatus::DELIVERY_MAN_ASSIGN1,'page'=>$request->page,'filter'=>$request->filter? $request->filter:'']) }}" method="post">
                @csrf
                <input type="hidden" value="" name="parcel_id" id="modal_parcel_id" class="modal_parcel_id" />
                <div class="modal-body">
                    <div class="form-group">
                        <label for="deliveryManSearch">{{ __('deliveryman.title')}}</label> <span class="text-danger">*</span>
                        <div class="form-control-wrap deliveryman-search">
                            <select id="deliveryManSearch" class="form-control deliveryManSearch" name="delivery_man_id" data-province_id="" data-url="{{ route('parcel.deliveryman.search') }}" >
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
                        <label for="note">{{ __('parcel.note')}}</label>
                        <div class="form-control-wrap deliveryman-search">
                            <textarea class="form-control" name="note" rows="5" id="note"></textarea>
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

{{-- RETURN_ASSIGN_TO_MERCHANT1 --}}
<div class="modal fade" id="parcelstatus{{ \App\Enums\ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1 }}" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" id="data-modal">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{ __('parcelStatus.'.\App\Enums\ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1) }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('parcel.dynamic.status.update', ['status'=>\App\Enums\ParcelStatus::RETURN_ASSIGN_TO_MERCHANT1,'page'=>$request->page,'filter'=>$request->filter? $request->filter:'']) }}" method="post">
                @csrf
                <input type="hidden" value="" name="parcel_id" id="modal_parcel_id" class="modal_parcel_id" />
                <div class="modal-body">
                    <div class="form-group pt-1">
                        <label for="date">{{ __('levels.date') }}</label> <span class="text-danger">*</span>
                        <input type="date" id="date" data-toggle="datepicker" name="date" data-parsley-trigger="change" placeholder="yyyy-mm-dd"  class="form-control" value="{{old('date')}}" require>
                        @error('date')
                            <small class="text-danger mt-2">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="note">{{ __('parcel.note')}}</label>
                        <div class="form-control-wrap deliveryman-search">
                            <textarea class="form-control" name="note" rows="5" id="note"></textarea>
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