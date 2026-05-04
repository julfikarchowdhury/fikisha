<div class="cbm-box cbm-item mb-3 pt-3">
    <div class="row align-items-center">
        <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12">
            <div class="form-group">
                <label for="items_package_type_id">{{ __('parcel.package_type') }} <span class="text-danger">*</span></label>
                <select id="items_package_type_id" name="items[{{ '0' . $key }}][package_type_id]" class="form-control w-100 select2 items_package_type_id">
                    <option value="1" @selected(old('package_type_id',$item->package_type_id) == 1)>{{ __('parcel.courier_document') }}</option>
                    <option value="2" @selected(old('package_type_id',$item->package_type_id) == 2)>{{ __('parcel.parcel_type') }}</option>
                </select>
                @error('package_type_id')
                    <span class="text-danger mt-2">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6  col-lg-3 col-xl-4 col-sm-12 mb-2">
            <label for="quantity">{{ __('parcel.quantity') }} <span class="text-danger">*</span></label>
            <div class="form-group">
                <input id="quantity" type="number" name="items[{{ '0' . $key }}][quantity]"
                    data-parsley-trigger="change" placeholder="{{ __('parcel.quantity') }}" autocomplete="off"
                    class="form-control" value="{{ old('quantity', $item->quantity) }}" required>
            </div>
            @error('quantity')
                <small class="text-danger mt-2">{{ $message }}</small>
            @enderror
            <div class="d-flex align-items-end justify-content-center h-100">
                <input type="hidden" id="item_total_weight" name="items[{{ '0' . $key }}][total_weight]" value="{{ $item->total_weight }}" />
                <input type="hidden" id="item_total_cbm" value="{{ $item->total_cbm }}" name="items[{{ '0' . $key }}][total_cbm]" />
                <input type="hidden" id="parcel_item_status" name="items[{{ '0' . $key }}][parcel_item_status]" value="{{ $item->parcel_item_status }}">
                <input type="hidden" id="shipping_fee_status" name="items[{{ '0' . $key }}][shipping_fee_status]" value="{{ $item->shipping_fee_status }}">
            </div>
        </div>
        <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12 mb-2 parcelTypeId {{ old('package_type_id',$item->package_type_id) == 2 ? 'd-block' : 'd-none' }}">
            <label for="local_weight">{{ __('parcel.weight') }} <span class="text-danger">*</span></label>
            <div class="form-group">
                <input id="local_weight" type="number" step="any" name="items[{{ '0' . $key }}][local_weight]"
                    data-parsley-trigger="change" placeholder="{{ __('parcel.weight') }}" autocomplete="off"
                    class="form-control" value="{{ old('local_weight', $item->weight) }}">
            </div>
            @error('local_weight')
                <small class="text-danger mt-2">{{ $message }}</small>
            @enderror
        </div>
        @if (SettingHelper('rush_hour_service_status') == \App\Enums\Status::ACTIVE)
            <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12">
                <div class="form-group mt-4">
                    <div class="preview-block">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input rush_hour_service" id="rush_hour_service{{ '0' . $key }}"
                                data-amount="{{ SettingHelper('rush_hour_service_charge') }}"
                                data-outside-amount="{{ SettingHelper('rush_hour_service_outside_charge') }}"
                                data-inside-distance="{{ settings()->inside_city_distance }}"
                                name="items[{{ '0' . $key }}][rush_hour_service]" onclick="rushHourServiceCheck(this);" @if($item->rush_hour_service) checked @endif
                                value="{{ $item->rush_hour_service }}">
                            <label class="custom-control-label" for="rush_hour_service{{ '0' . $key }}">{{ __('parcel.rush_hour_service') }}</label>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12">
            <div class="form-group mt-4">
                <div class="preview-block">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input extra_cost" id="extra_cost{{ '0' . $key }}"
                            name="items[{{ '0' . $key }}][extra_cost]" @if($item->extra_cost) checked @endif>
                        <label class="custom-control-label" for="extra_cost{{ '0' . $key }}">{{ __('parcel.extra_cost') }}</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12 extraCostCheck {{ old('extra_cost',$item->extra_cost) ? 'd-block' : 'd-none' }}">
            <div class="form-group">
                <label for="extra_cost_amount">{{ __('parcel.extra_cost_amount') }}<span class="text-danger">*</span></label>
                <input type="number" name="items[{{ '0' . $key }}][extra_cost_amount]" value="{{ old('extra_cost_amount',$item->extra_cost_amount) }}" class="form-control extra_cost_amount" placeholder="{{ __('parcel.extra_cost_amount') }}">
            </div>
        </div>
        <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12 extraCostCheck {{ old('extra_cost',$item->extra_cost) ? 'd-block' : 'd-none' }}">
            <div class="form-group">
                <label for="extra_cost_description">{{ __('parcel.extra_cost_description') }}</label>
                <input type="text" name="items[{{ '0' . $key }}][extra_cost_description]" value="{{ old('extra_cost_description',$item->extra_cost_description) }}" class="form-control extra_cost_description" placeholder="{{ __('parcel.extra_cost_description') }}">
            </div>
        </div>
        <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12">
            <div class="form-group">
                <label for="packaging_id{{ '0' . $key }}">{{ __('parcel.packaging') }}</label>
                <select id="packaging_id{{ '0' . $key }}" class="form-control select2 packaging_id" name="items[{{ '0' . $key }}][packaging_id]" onchange="packagingServiceCheck(this);">
                    <option value=""> {{ __('menus.select') }} {{ __('menus.packaging') }}</option>
                    @foreach ($packagings as $packaging)
                        <option data-packagingamount="{{ $packaging->price }}" value="{{ $packaging->id }}" @selected(old('packaging_id',$item->packaging_id) == $packaging->id)>
                            {{ $packaging->name }} ( {{ number_format($packaging->price, 2) }} {{ settings()->currency }})
                        </option>
                    @endforeach
                </select>
                @error('packaging_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12">
            <div class="form-group">
                <label for="content_parcel">{{ __('parcel.content_parcel') }}</label>
                <input id="content_parcel" type="text" name="items[{{ '0' . $key }}][content_parcel]" placeholder="{{ __('parcel.content_parcel') }}" autocomplete="off" class="form-control" value="{{ old('content_parcel',$item->content_parcel) }}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-end">
            <button type="button" class="clone-parcel-item text-white btn btn-info mt-2">
                <i class="fa fa-clone me-2"></i> {{ __('parcel.copy_this_parcel') }}
            </button>
            <button type="button" class="remove-parcel-item mt-2 text-white btn btn-danger">
                <i class="fa fa-times me-2"></i> {{ __('levels.remove_item') }}
            </button>
        </div>
    </div>
</div>
