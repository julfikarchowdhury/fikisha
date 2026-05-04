@extends('backend.partials.master')
@section('title')
    {{ __('parcel.shipping_type') }} {{ __('levels.add') }}
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
                        <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">{{ __('parcel.shipping_type') }} {{ __('levels.create') }}</h4>
                    </div>
                    <div class="card-body">
                        <!-- <h2 class="pageheader-title">{{ __('parcel.shipping_type') }} {{ __('levels.create') }}</h2> -->
                        <form action="{{ route('shipping-type.store') }}" method="POST" enctype="multipart/form-data"
                            id="basicform">
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="delivery_type_id">{{ __('menus.delivery_type') }}</label> <span
                                        class="text-danger">*</span>
                                    <select id="delivery_type_id" name="delivery_type_id" required
                                        class="form-control select2">
                                        <option>{{ __('levels.select') }} {{ __('menus.delivery_type') }}</option>
                                        @foreach ($delivery_types as $delivery_type)
                                            <option value="{{ $delivery_type->id }}" @selected(old('delivery_type_id') == $delivery_type->id)>
                                                {{ $delivery_type->title }}</option>
                                        @endforeach
                                    </select>
                                    @error('delivery_type_id')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="title">{{ __('levels.title') }}</label> <span
                                        class="text-danger">*</span>
                                    <input id="title" type="text" name="title" data-parsley-trigger="change"
                                        placeholder="{{ __('placeholder.Enter_title') }}" autocomplete="off"
                                        class="form-control" value="{{ old('title') }}" require>
                                    @error('title')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group  col-md-4">
                                    <label for="basic_price">{{ __('levels.basic_price') }} <span
                                            class="text-danger">*</span> </label>
                                    <input id="basic_price" type="text" name="basic_price" data-parsley-trigger="change"
                                        placeholder="{{ __('levels.Enter_basic_price') }}" autocomplete="off"
                                        class="form-control" value="{{ old('basic_price') }}" require>
                                    @error('basic_price')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group  col-md-4">
                                    <label for="basic_price">{{ __('levels.slots') }} <span
                                            class="text-danger">*</span> </label>
                                    <input id="slots" type="text" name="slots" data-parsley-trigger="change"
                                        placeholder="{{ __('levels.Enter_slots') }}" autocomplete="off"
                                        class="form-control" value="{{ old('slots') }}" require>
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
                                                class="form-control" value="{{ old('start_weight') }}" require>
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
                                                class="form-control" value="{{ old('end_weight') }}" require>
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
                                                class="form-control" value="{{ old('addi_weight_price') }}" require>
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
                                                class="form-control" value="{{ old('start_volume') }}" require>
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
                                                class="form-control" value="{{ old('end_volume') }}" require>
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
                                                value="{{ old('addi_volume_price') }}" require>
                                            @error('addi_volume_price')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                    <h4 class="mt-2">Distance pricing (km)</h4>
                                    <div class="row mt-3">
                                        <div class="form-group  col-md-4">
                                            <label for="start_distance">{{ __('levels.start_distance') }} <span
                                                    class="text-danger">*</span> </label>
                                            <input id="start_distance" type="text" name="start_distance"
                                                data-parsley-trigger="change"
                                                placeholder="{{ __('levels.Enter_start_distance') }}" autocomplete="off"
                                                class="form-control" value="{{ old('start_distance') }}" require>
                                            @error('start_distance')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>


                                        <div class="form-group  col-md-4">
                                            <label for="end_distance">{{ __('levels.end_distance') }} <span
                                                    class="text-danger">*</span> </label>
                                            <input id="end_distance" type="text" name="end_distance"
                                                data-parsley-trigger="change"
                                                placeholder="{{ __('levels.Enter_end_distance') }}" autocomplete="off"
                                                class="form-control" value="{{ old('end_distance') }}" require>
                                            @error('end_distance')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>

                                        <div class="form-group  col-md-4">
                                            <label for="addi_distance_price">{{ __('levels.addi_price') }}<span
                                                    class="text-danger">*</span> <small>Per (start - end = distance)
                                                </small> </label>
                                            <input id="addi_distance_price" type="text" name="addi_distance_price"
                                                data-parsley-trigger="change"
                                                placeholder="{{ __('levels.Enter_addi_distance_price') }}"
                                                autocomplete="off" class="form-control"
                                                value="{{ old('addi_distance_price') }}" require>
                                            @error('addi_distance_price')
                                                <small class="text-danger mt-2">{{ $message }}</small>
                                            @enderror
                                        </div>
                                    </div>
                                </div>



                            </div>

                            <div class="row mt-3">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 ">
                                    <button type="submit"
                                        class="btn btn-space btn-primary">{{ __('levels.save') }}</button>
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
        $(document).on('click', '.fa.fa-times', function() {
            $(this).closest('.row').remove();
        });

        var number = {{ old('options') && count(old('options')) > 0 ? count(old('options')) : 1 }};
        $('#add-options').on('click', function() {
            number++;
            var row = '';
            row += '<div class="row mt-3">';
            row += '<div class="col-md-3">';
            row += '<label  >{{ __('levels.from_weight') }}</label>';
            row +=
                '<input   type="number" name="options[' + number +
                '][from_kg]" step="any" data-parsley-trigger="change" placeholder="{{ __('placeholder.Enter_from_weight') }}" autocomplete="off" class="form-control"  required>';
            row += '</div>';
            row += '<div class="col-md-3">';
            row += '<label  >{{ __('levels.to_weight') }}</label>';
            row +=
                ' <input type="number" name="options[' + number +
                '][to_kg]" step="any" data-parsley-trigger="change" placeholder="{{ __('placeholder.enter_to_weight') }}" autocomplete="off" class="form-control"  required>';
            row += '</div>';
            row += '<div class="col-md-3">';
            row += '<label  >{{ __('levels.price') }} (Per distance)</label>';
            row +=
                '<input  type="number" step="any" name="options[' + number +
                '][price]" data-parsley-trigger="change" placeholder="{{ __('placeholder.enter_price') }}" autocomplete="off" class="form-control"  required>';
            row += '</div>';
            row += '<div class="col-md-3">';
            row += '<i class="fa fa-times"></i>';
            row += '</div>';
            row += '</div>';
            $('#all-options').append(row);

        });
    </script>
@endpush
