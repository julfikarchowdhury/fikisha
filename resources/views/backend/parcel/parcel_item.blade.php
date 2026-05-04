<div class="cbm-box cbm-item mb-3 pt-3">
    <div class="row align-items-center">
        <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12">
            <div class="form-group">
                <label for="items_package_type_id">{{ __('parcel.package_type') }} <span class="text-danger">*</span></label>
                <select id="items_package_type_id" name="items[{{ $item_number }}][package_type_id]" class="form-control w-100 select2 items_package_type_id">
                    <option value="1" @selected(old('package_type_id') == 1)>{{ __('parcel.courier_document') }}</option>
                    <option value="2" @selected(old('package_type_id') == 2)>{{ __('parcel.parcel_type') }}</option>
                </select>
                @error('package_type_id')
                    <span class="text-danger mt-2">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12 mb-2">
            <label for="quantity">{{ __('parcel.quantity') }} <span class="text-danger">*</span></label>
            <div class="form-group">
                <input id="quantity" type="number" name="items[{{ $item_number }}][quantity]" placeholder="{{ __('parcel.quantity') }}" autocomplete="off"
                    class="form-control" value="{{ old('quantity', 1) }}" required>
            </div>
            <div class="d-flex align-items-end justify-content-center h-100">
                <input type="hidden" id="item_total_weight" name="items[{{ $item_number }}][total_weight]" value="0" />
                <input type="hidden" id="item_total_cbm" value="0" name="items[{{ $item_number }}][total_cbm]" />
                <input type="hidden" id="parcel_item_status" name="items[{{ $item_number }}][parcel_item_status]" value="{{ old('parcel_item_status', App\Enums\ParcelItemStatus::PENDING) }}">
                <input type="hidden" id="shipping_fee_status" name="items[{{ $item_number }}][shipping_fee_status]" value="{{ old('shipping_fee_status', App\Enums\ParcelItemStatus::PENDING) }}">
            </div>
        </div>
        <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12 mb-2 parcelTypeId {{ old('package_type_id') == 2 ? 'd-block' : 'd-none' }}">
            <label for="local_weight">{{ __('parcel.weight') }} <span class="text-danger">*</span></label>
            <div class="form-group">
                <input id="local_weight" type="number" step="any" name="items[{{ $item_number }}][local_weight]" placeholder="{{ __('parcel.weight') }}" autocomplete="off"
                    class="form-control" value="{{ old('local_weight') }}">
            </div>
            @error('local_weight')
                <small class="text-danger mt-2">{{ $message }}</small>
            @enderror
        </div>
        <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12">
            <div class="form-group mt-4">
                <div class="preview-block">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input extra_cost" id="extra_cost{{ $item_number }}"
                            name="items[{{ $item_number }}][extra_cost]">
                        <label class="custom-control-label" for="extra_cost{{ $item_number }}">{{ __('parcel.extra_cost') }}</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12 extraCostCheck {{ old('extra_cost') ? 'd-block' : 'd-none' }}">
            <div class="form-group">
                <label for="extra_cost_amount">{{ __('parcel.extra_cost_amount') }}<span class="text-danger">*</span></label>
                <input type="number" name="items[{{ $item_number }}][extra_cost_amount]" class="form-control extra_cost_amount" placeholder="{{ __('parcel.extra_cost_amount') }}">
            </div>
        </div>
        <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12 extraCostCheck {{ old('extra_cost') ? 'd-block' : 'd-none' }}">
            <div class="form-group">
                <label for="extra_cost_description">{{ __('parcel.extra_cost_description') }}</label>
                <input type="text" name="items[{{ $item_number }}][extra_cost_description]" class="form-control extra_cost_description" placeholder="{{ __('parcel.extra_cost_description') }}">
            </div>
        </div>
        <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12">
            <div class="form-group">
                <label for="content_parcel">{{ __('parcel.content_parcel') }}</label>
                <input id="content_parcel" type="text" name="items[{{ $item_number }}][content_parcel]" placeholder="{{ __('parcel.content_parcel') }}" autocomplete="off" class="form-control" value="{{ old('content_parcel') }}">
            </div>
        </div>
        <div class="col-md-6 col-lg-3 col-xl-4 col-sm-12">
            <div class="form-group">
                <label for="parcel_value_item_{{ $item_number }}">Parcel Value Amount</label>
                <input id="parcel_value" type="number" step="0.01" name="items[{{ $item_number }}][parcel_value]" placeholder="0.00" autocomplete="off" class="form-control" value="{{ old('parcel_value') }}">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-end">
            <button type="button" class="clone-parcel-item text-white btn btn-info mt-2">
                <i class="fa fa-clone me-2"></i>{{ __('parcel.copy_this_parcel') }}
            </button>
            <button type="button" class="remove-parcel-item mt-2 text-white btn btn-danger">
                <i class="fa fa-trash me-2"></i>{{ __('levels.remove_parcel') }}
            </button>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $(".select2").select2();
    });
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
</script>
