@extends('backend.partials.master')
@section('title')
    {{ __('levels.dispute') ?? 'Dispute' }} {{ __('levels.add') ?? 'Add' }}
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}" class="breadcrumb-link">{{ __('levels.dashboard') }}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('merchant-panel.parcel.details', $parcel->id) }}" class="breadcrumb-link">{{ __('parcel.parcel_details') ?? 'Parcel Details' }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                {{ __('levels.dispute') ?? 'Dispute' }}
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">
                        Raise Dispute for Parcel {{ $parcel->tracking_id ?? '#'.$parcel->id }}
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('merchant-panel.parcel.dispute.store', $parcel->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="reason_type">Reason Type</label> <span class="text-danger">*</span>
                                    <select id="reason_type" name="reason_type" class="form-control @error('reason_type') is-invalid @enderror" required>
                                        <option value="" disabled {{ old('reason_type') ? '' : 'selected' }}>Select reason</option>
                                        <option value="delivery_issue" {{ old('reason_type') == 'delivery_issue' ? 'selected' : '' }}>Delivery issue</option>
                                        <option value="damaged_parcel" {{ old('reason_type') == 'damaged_parcel' ? 'selected' : '' }}>Damaged parcel</option>
                                        <option value="lost_parcel" {{ old('reason_type') == 'lost_parcel' ? 'selected' : '' }}>Lost parcel</option>
                                        <option value="wrong_item" {{ old('reason_type') == 'wrong_item' ? 'selected' : '' }}>Wrong item</option>
                                        <option value="not_delivered" {{ old('reason_type') == 'not_delivered' ? 'selected' : '' }}>Not delivered</option>
                                        <option value="payment_issue" {{ old('reason_type') == 'payment_issue' ? 'selected' : '' }}>Payment issue</option>
                                        <option value="rider_behavior" {{ old('reason_type') == 'rider_behavior' ? 'selected' : '' }}>Rider behavior</option>
                                        <option value="other" {{ old('reason_type') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('reason_type')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea id="description" name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                                    @error('description')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="evidence_files">Evidence Files (optional)</label>
                                    <input id="evidence_files" type="file" name="evidence_files[]" class="form-control" multiple>
                                    <small class="text-muted d-block mt-1">JPG, PNG, or PDF up to 5MB each.</small>
                                    @error('evidence_files.*')
                                        <small class="text-danger mt-2">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">{{ __('levels.submit') ?? 'Submit' }}</button>
                            <a href="{{ route('merchant-panel.parcel.details', $parcel->id) }}" class="btn btn-light">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
