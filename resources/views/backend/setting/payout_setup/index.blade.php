@extends('backend.partials.master')
@section('title')
{{ __('menus.pay_out') }} {{ __('menus.settings') }}
@endsection

@push('scripts')
<script>
    $('#mpesaTestTokenBtn').on('click', function () {
        $.post('{{ route('mpesa.test.token') }}', {
            _token: $('meta[name="csrf-token"]').attr('content')
        }).done(function (data) {
            if (data && data.success) {
                toastr.success(data.message || 'M-Pesa token is valid.');
            } else {
                toastr.error((data && data.message) ? data.message : 'M-Pesa token test failed.');
            }
        }).fail(function () {
            toastr.error('M-Pesa token test failed.');
        });
    });
</script>
@endpush
@section('maincontent')
<div class="container-fluid  dashboard-content">
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="page-header">
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard.index')}}" class="breadcrumb-link">{{ __('menus.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link active">{{ __('menus.settings') }}</a></li>
                            <li class="breadcrumb-item"><a href="" class="breadcrumb-link active">{{ __('menus.pay_out') }}</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        
        <div class="col-lg-6  col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">M-Pesa (Daraja)</h4>
                </div>
                <div class="card-body">
                    @if(hasPermission('payout_setup_settings_update'))
                    <form action="{{route('payout.setup.settings.update',\App\Enums\PayoutSetup::MPESA)}}" method="POST" enctype="multipart/form-data" id="basicform">
                        @method('PUT')
                        @csrf
                        @endif
                        <div class="row">
                            <div class="col-12 ">
                                <div class="form-group">
                                    <label for="mpesa_environment">Environment</label> <span class="text-danger">*</span>
                                    <select id="mpesa_environment" name="mpesa_environment" class="form-control @error('mpesa_environment') is-invalid @enderror">
                                        <option value="sandbox" @selected(old('mpesa_environment', globalSettings('mpesa_environment')) === 'sandbox')>Sandbox</option>
                                        <option value="live" @selected(old('mpesa_environment', globalSettings('mpesa_environment')) === 'live')>Live</option>
                                    </select>
                                    @error('mpesa_environment')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="mpesa_consumer_key">Consumer Key</label> <span class="text-danger">*</span>
                                    <input id="mpesa_consumer_key" type="text" name="mpesa_consumer_key" placeholder="Consumer Key"
                                        autocomplete="off" class="form-control @error('mpesa_consumer_key') is-invalid @enderror"
                                        value="{{ old('mpesa_consumer_key', globalSettings('mpesa_consumer_key')) }}" require>
                                    @error('mpesa_consumer_key')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="mpesa_consumer_secret">Consumer Secret</label> <span class="text-danger">*</span>
                                    <input id="mpesa_consumer_secret" type="text" name="mpesa_consumer_secret" placeholder="Consumer Secret"
                                        autocomplete="off" class="form-control @error('mpesa_consumer_secret') is-invalid @enderror"
                                        value="{{ old('mpesa_consumer_secret', globalSettings('mpesa_consumer_secret')) }}" require>
                                    @error('mpesa_consumer_secret')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="mpesa_shortcode">Shortcode</label> <span class="text-danger">*</span>
                                    <input id="mpesa_shortcode" type="text" name="mpesa_shortcode" placeholder="Shortcode"
                                        autocomplete="off" class="form-control @error('mpesa_shortcode') is-invalid @enderror"
                                        value="{{ old('mpesa_shortcode', globalSettings('mpesa_shortcode')) }}" require>
                                    @error('mpesa_shortcode')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="mpesa_passkey">Passkey</label>
                                    <input id="mpesa_passkey" type="text" name="mpesa_passkey" placeholder="Lipa Na M-Pesa Passkey"
                                        autocomplete="off" class="form-control @error('mpesa_passkey') is-invalid @enderror"
                                        value="{{ old('mpesa_passkey', globalSettings('mpesa_passkey')) }}">
                                    @error('mpesa_passkey')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="mpesa_callback_url">Callback URL</label>
                                    <input id="mpesa_callback_url" type="text" name="mpesa_callback_url" placeholder="https://example.com/callback"
                                        autocomplete="off" class="form-control @error('mpesa_callback_url') is-invalid @enderror"
                                        value="{{ old('mpesa_callback_url', globalSettings('mpesa_callback_url')) }}">
                                    @error('mpesa_callback_url')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="form-group d-flex">
                                    <label for="switch-id-mpesa">{{ __('levels.status') }}</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input switch-id ml-3" name="mpesa_status" id="switch-id-mpesa"
                                            type="checkbox" role="switch"
                                            @if(old('mpesa_status', globalSettings('mpesa_status'))==\App\Enums\Status::ACTIVE) checked @endif>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(hasPermission('payout_setup_settings_update'))
                        <div class="row pt-4">
                            <div class="col-12 d-flex justify-content-between">
                                <button type="button" id="mpesaTestTokenBtn" class="btn btn-space btn-outline-secondary">Test M-Pesa Token</button>
                                <button type="submit" class="btn btn-space btn-primary">{{ __('levels.save_change') }}</button>
                            </div>
                        </div>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection()