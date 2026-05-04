@extends('backend.partials.master')
@section('title')
{{ __('parcel.shipping_type') }} {{ __('levels.edit') }}
@endsection
@section('maincontent')
<div class="container-fluid  dashboard-content">
    <!-- pageheader -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="page-header">
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                    class="breadcrumb-link">{{ __('levels.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="#"
                                    class="breadcrumb-link">{{ __('menus.settings') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('shipping-type.index') }}"
                                    class="breadcrumb-link">{{ __('parcel.shipping_type') }}</a></li>
                            <li class="breadcrumb-item"><a href=""
                                    class="breadcrumb-link active">{{ __('levels.create') }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- end pageheader -->

    <div class="row">
        <!-- basic form -->
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">
                        @if ($shipping_type->delivery_type_id == 1)
                        {{ __('parcel.inside_city') }}:
                        @elseif($shipping_type->delivery_type_id == 3)
                        {{ __('parcel.outside_city') }}:
                        @endif
                        {{ $shipping_type->title }}
                        {{ __('levels.edit') }}
                    </h4>
                </div>
                <div class="card-body">
                    <!-- <h2 class="pageheader-title">
                            @if ($shipping_type->delivery_type_id == 1)
                                {{ __('parcel.inside_city') }}:
                            @elseif($shipping_type->delivery_type_id == 3)
                                {{ __('parcel.outside_city') }}:
                            @endif
                            {{ $shipping_type->title }}
                            {{ __('levels.edit') }}
                        </h2> -->
                    <form action="{{ route('shipping-type.update', ['id' => $shipping_type->id, 'delivery_type_id' => $shipping_type->delivery_type_id]) }}"
                        method="POST" enctype="multipart/form-data" id="basicform">
                        @csrf
                        @method('put')

                        <div class="row">

                            <div class="form-group col-md-4">
                                <label for="title">{{ __('levels.title') }}</label> <span
                                    class="text-danger">*</span>
                                <input id="title" type="text" name="title" data-parsley-trigger="change"
                                    placeholder="{{ __('placeholder.Enter_title') }}" autocomplete="off"
                                    class="form-control" value="{{ old('title', $shipping_type->title) }}" require>
                                @error('title')
                                <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>
                            @if($shipping_type->delivery_type_id != 3)
                            <div class="form-group  col-md-4">
                                <label for="basic_price">{{ __('levels.basic_price') }} <span
                                        class="text-danger">*</span> </label>
                                <input id="basic_price" type="text" name="basic_price" data-parsley-trigger="change"
                                    placeholder="{{ __('levels.Enter_basic_price') }}" autocomplete="off"
                                    class="form-control" value="{{ old('basic_price', $shipping_type->basic_price) }}"
                                    require>
                                @error('basic_price')
                                <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>
                            @endif

                            <div class="form-group  col-md-4">
                                <label for="basic_price">{{ __('levels.slots') }} <span
                                        class="text-danger">*</span> </label>
                                <input id="slots" type="text" name="slots" data-parsley-trigger="change"
                                    placeholder="{{ __('levels.Enter_slots') }}" autocomplete="off"
                                    class="form-control" value="{{ old('slots',$shipping_type->slots) }}" require>
                                @error('slots')
                                <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>



                            <div class="col-12">
                                <h4 class="mt-2">Weight Based Pricing (kg)</h4>
                                <div class="row mt-3">
                                    <div class="form-group  col-md-4">
                                        <label for="start_weight">{{ __('levels.start_weight') }} <span
                                                class="text-danger">*</span> </label>
                                        <input id="start_weight" type="text" name="start_weight"
                                            data-parsley-trigger="change"
                                            placeholder="{{ __('levels.Enter_start_weight') }}" autocomplete="off"
                                            class="form-control"
                                            value="{{ old('start_weight', $shipping_type->start_weight) }}" require>
                                        @error('start_weight')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                        @enderror
                                    </div>


                                    <div class="form-group  col-md-4">
                                        <label for="end_weight">{{ __('levels.end_weight') }} <span
                                                class="text-danger">*</span> </label>
                                        <input id="end_weight" type="text" name="end_weight"
                                            data-parsley-trigger="change"
                                            placeholder="{{ __('levels.Enter_end_weight') }}" autocomplete="off"
                                            class="form-control"
                                            value="{{ old('end_weight', $shipping_type->end_weight) }}" require>
                                        @error('end_weight')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group  col-md-4">
                                        <label for="addi_weight_price">{{ __('levels.addi_price') }}<span
                                                class="text-danger">*</span> <small>Per (start - end = weight) </small>
                                        </label>
                                        <input id="addi_weight_price" type="text" name="addi_weight_price"
                                            data-parsley-trigger="change"
                                            placeholder="{{ __('levels.Enter_addi_weight_price') }}" autocomplete="off"
                                            class="form-control"
                                            value="{{ old('addi_weight_price', $shipping_type->addi_weight_price) }}"
                                            require>
                                        @error('addi_weight_price')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                <h4 class="mt-2">Volume based pricing (Cubic meters)</h4>
                                <div class="row mt-3">
                                    <div class="form-group  col-md-4">
                                        <label for="start_volume">{{ __('levels.start_volume') }} <span
                                                class="text-danger">*</span> </label>
                                        <input id="start_volume" type="text" name="start_volume"
                                            data-parsley-trigger="change"
                                            placeholder="{{ __('levels.Enter_start_volume') }}" autocomplete="off"
                                            class="form-control"
                                            value="{{ old('start_volume', $shipping_type->start_volume) }}" require>
                                        @error('start_volume')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                        @enderror
                                    </div>


                                    <div class="form-group  col-md-4">
                                        <label for="end_volume">{{ __('levels.end_volume') }} <span
                                                class="text-danger">*</span> </label>
                                        <input id="end_volume" type="text" name="end_volume"
                                            data-parsley-trigger="change"
                                            placeholder="{{ __('levels.Enter_end_volume') }}" autocomplete="off"
                                            class="form-control"
                                            value="{{ old('end_volume', $shipping_type->end_volume) }}" require>
                                        @error('end_volume')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="form-group  col-md-4">
                                        <label for="addi_volume_price">{{ __('levels.addi_price') }}<span
                                                class="text-danger">*</span> <small>Per (start - end = cubic meters)
                                            </small> </label>
                                        <input id="addi_volume_price" type="text" name="addi_volume_price"
                                            data-parsley-trigger="change"
                                            placeholder="{{ __('levels.Enter_addi_volume_price') }}"
                                            autocomplete="off" class="form-control"
                                            value="{{ old('addi_volume_price', $shipping_type->addi_volume_price) }}"
                                            require>
                                        @error('addi_volume_price')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>
                                @if($shipping_type->delivery_type_id == 3)
                                <h4 class="mt-3">{{ __('parcel.basic_price') }} : </h4>
                                <div id="all-options">
                                    @if ($shipping_type->ShippingChargeOptions->count() > 0)
                                    @foreach ($shipping_type->ShippingChargeOptions as $key => $option)
                                    @include('backend.shippingtype.old_options', [
                                    'option' => $option,
                                    'key' => $key + 1,
                                    'option_id' => $option->id,
                                    ])
                                    @endforeach
                                    @else
                                    <div class="row mt-3">
                                        <div class="col-md-3">
                                            <label>{{ __('levels.from_km') }}</label>
                                            <input type="number" name="options[1][from_km]" step="any"
                                                placeholder="{{ __('placeholder.Enter_from_km') }}" autocomplete="off"
                                                class="form-control" required>
                                            @error('from_km')
                                            <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label>{{ __('levels.to_km') }}</label>
                                            <input type="number" name="options[1][to_km]" step="any"
                                                placeholder="{{ __('placeholder.enter_to_km') }}" autocomplete="off"
                                                class="form-control" required>
                                            @error('to_km')
                                            <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label>{{ __('parcel.basic_price') }}</label>
                                            <input type="number" step="any" name="options[1][basic_price]"
                                                placeholder="{{ __('placeholder.enter_basic_price') }}"
                                                autocomplete="off" class="form-control" required>
                                            @error('basic_price')
                                            <p class="text-danger">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <button class="btn btn-sm btn-primary mt-4" type="button" id="add-options"><i class="fa fa-plus"></i></button>
                                        </div>
                                    </div>

                                    @endif
                                </div>
                                @endif

                            </div>
                        </div>
                </div>


                <div class="row my-3 ms-2">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 ">
                        <button type="submit" class="btn btn-space btn-primary">{{ __('levels.save') }}</button>
                        <a href="{{ route('shipping-type.index') }}"
                            class="btn btn-space btn-secondary">{{ __('levels.cancel') }}</a>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- end basic form -->
</div>
</div>
@endsection
@push('styles')
<style>
    .fa.fa-times {
        margin-top: 35px;
        font-size: 20px;
    }

    .fa.fa-times:hover {
        cursor: pointer;
    }
</style>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script type="text/javascript">
    var number = {
        {
            $shipping_type - > ShippingChargeOptions - > count() > 0 ? $shipping_type - > ShippingChargeOptions - > count() : 1
        }
    };
    $('#add-options').on('click', function() {
        number++;
        var row = '';
        row += '<div class="row mt-3">';
        row += '<div class="col-md-3">';
        row += '<label  >{{ __('
        levels.from_km ') }}</label>';
        row +=
            '<input   type="number" name="options[' + number +
            '][from_km]" step="any" data-parsley-trigger="change" placeholder="{{ __('
        placeholder.Enter_from_km ') }}" autocomplete="off" class="form-control"  required>';
        row += '</div>';
        row += '<div class="col-md-3">';
        row += '<label  >{{ __('
        levels.to_km ') }}</label>';
        row +=
            ' <input type="number" name="options[' + number +
            '][to_km]" step="any" data-parsley-trigger="change" placeholder="{{ __('
        placeholder.enter_to_km ') }}" autocomplete="off" class="form-control"  required>';
        row += '</div>';
        row += '<div class="col-md-3">';
        row += '<label  >{{ __('
        levels.basic_price ') }}</label>';
        row +=
            '<input  type="number" step="any" name="options[' + number +
            '][basic_price]" data-parsley-trigger="change" placeholder="{{ __('
        placeholder.enter_basic_price ') }}" autocomplete="off" class="form-control"  required>';
        row += '</div>';
        row += '<div class="col-md-3">';
        row += '<i class="fa fa-times"></i>';
        row += '</div>';
        row += '</div>';
        $('#all-options').append(row);

    });


    $(document).on('click', '.fa.fa-times', function() {
        $(this).closest('.row').remove();
        number--;
    });
</script>
@endpush