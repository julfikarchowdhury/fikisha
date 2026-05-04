@extends('backend.partials.master')
@section('title')
    M-Pesa Parcel Payment
@endsection

@section('maincontent')
    <div class="container-fluid dashboard-content">
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="page-header">
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dashboard.index') }}" class="breadcrumb-link">{{ __('levels.dashboard') }}</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('merchant-panel.parcel.index') }}" class="breadcrumb-link">{{ __('parcel.title') }}</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    M-Pesa Payment
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-8 col-lg-10 col-md-12 col-sm-12 col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Complete Parcel Payment</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div><strong>Tracking ID:</strong> {{ $parcel->tracking_id }}</div>
                            <div><strong>Amount:</strong> {{ settings()->currency }} {{ number_format((float) ($parcel->final_paid_amount ?? $parcel->total_delivery_amount ?? 0), 2) }}</div>
                            <div><strong>Status:</strong> {{ trans('parcelStatusShow.' . (int) $parcel->status) }}</div>
                        </div>

                        <form action="{{ route('merchant-panel.mpesa.pay.parcel', $parcel->id) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="who_pays_either">Who Pays <span class="text-danger">*</span></label>
                                <select name="who_pays_either" id="who_pays_either" class="form-control @error('who_pays_either') is-invalid @enderror" required>
                                    <option value="{{ \App\Enums\WhoPays::SENDER }}" @selected((int) $selectedWhoPays === \App\Enums\WhoPays::SENDER)>Sender</option>
                                    <option value="{{ \App\Enums\WhoPays::RECIPIENT }}" @selected((int) $selectedWhoPays === \App\Enums\WhoPays::RECIPIENT)>Receiver</option>
                                </select>
                                @error('who_pays_either')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="phone">M-Pesa Number <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    id="phone"
                                    name="phone"
                                    placeholder="07XXXXXXXX or 2547XXXXXXXX"
                                    value="{{ old('phone', $defaultPhone) }}"
                                    required
                                >
                                @error('phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('merchant-panel.parcel.index') }}" class="btn btn-secondary mr-2">{{ __('levels.cancel') }}</a>
                                <button type="submit" class="btn btn-success">Regenerate M-Pesa Prompt</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        var senderPhone = @json($senderPhone);
        var receiverPhone = @json($receiverPhone);
        var phoneInput = document.getElementById('phone');
        var whoPaysSelect = document.getElementById('who_pays_either');
        if (!phoneInput || !whoPaysSelect) {
            return;
        }
        whoPaysSelect.addEventListener('change', function () {
            if (String(this.value) === String({{ \App\Enums\WhoPays::RECIPIENT }})) {
                phoneInput.value = receiverPhone || '';
            } else {
                phoneInput.value = senderPhone || '';
            }
        });
    })();
</script>
@endpush
