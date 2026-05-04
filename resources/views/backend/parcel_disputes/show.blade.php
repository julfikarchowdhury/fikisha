@extends('backend.partials.master')
@section('title')
    Dispute Details
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
                                <a href="{{ route('admin.disputes.index') }}" class="breadcrumb-link">Disputes</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Details</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Dispute Info</h5>
                </div>
                <div class="card-body">
                    <p><strong>ID:</strong> {{ $dispute->id }}</p>
                    <p><strong>Raised By:</strong> {{ ucfirst($dispute->raised_by) }}</p>
                    <p><strong>Reason:</strong> {{ ucfirst(str_replace('_', ' ', $dispute->reason_type)) }}</p>
                    <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $dispute->status)) }}</p>
                    <p><strong>Description:</strong> {{ $dispute->description ?: '-' }}</p>
                    <p><strong>Created:</strong> {{ $dispute->created_at?->format('Y-m-d H:i') }}</p>
                    <p><strong>Resolved:</strong> {{ $dispute->resolved_at?->format('Y-m-d H:i') ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Parcel Info</h5>
                </div>
                <div class="card-body">
                    @if ($parcel)
                        <p><strong>Tracking ID:</strong> {{ $parcel->tracking_id }}</p>
                        <p><strong>Status:</strong> {!! $parcel->parcel_status !!}</p>
                        <p><strong>Payment Status:</strong>
                            @if ($parcel->payment_status == App\Enums\InvoiceStatus::PAID)
                                Paid
                            @elseif ($parcel->payment_status == App\Enums\InvoiceStatus::PROCESSING)
                                Processing
                            @elseif ($parcel->payment_status == App\Enums\InvoiceStatus::UNPAID)
                                Unpaid
                            @else
                                {{ $parcel->payment_status }}
                            @endif
                        </p>
                        <p><strong>Final Amount:</strong> {{ settings()->currency }} {{ number_format((float) ($parcel->final_paid_amount ?? 0), 2) }}</p>
                        <p><strong>Sender:</strong> {{ optional($parcel->merchant)->business_name ?? '-' }}</p>
                        <p><strong>Receiver:</strong> {{ $parcel->customer_name ?? '-' }} ({{ $parcel->customer_phone ?? '-' }})</p>
                        <p><strong>Rider:</strong>
                            {{ $rider?->user?->name ?? '-' }}
                            @if ($rider?->user?->mobile)
                                ({{ $rider->user->mobile }})
                            @endif
                        </p>
                    @else
                        <p>Parcel not found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Parcel Timeline</h5>
                </div>
                <div class="card-body">
                    @if (!empty($timeline) && $timeline->count())
                        <ul class="list-unstyled mb-0">
                            @foreach ($timeline as $event)
                                <li class="mb-2">
                                    <strong>{{ $event->status_name }}</strong>
                                    <span class="text-muted">— {{ $event->created_at?->format('Y-m-d H:i') }}</span>
                                    @if ($event->deliveryMan?->user?->name)
                                        <div class="small text-muted">Rider: {{ $event->deliveryMan->user->name }}</div>
                                    @endif
                                    @if (!empty($event->note))
                                        <div class="small">{{ $event->note }}</div>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mb-0">No timeline events found.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Rider Info</h5>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $rider?->user?->name ?? '-' }}</p>
                    <p><strong>Phone:</strong> {{ $rider?->user?->mobile ?? '-' }}</p>
                    <p><strong>Email:</strong> {{ $rider?->user?->email ?? '-' }}</p>
                    <p><strong>Rider ID:</strong> {{ $rider?->id ?? '-' }}</p>
                    <p><strong>Wallet Balance:</strong>
                        {{ settings()->currency }} {{ number_format((float) ($wallet->balance ?? 0), 2) }}
                    </p>
                    <p><strong>Rider Disputes:</strong> {{ $riderDisputeCount ?? 0 }}</p>
                    <p><strong>Confirmed Losses:</strong> {{ $riderDisputeLossCount ?? 0 }}</p>
                    <p><strong>Last Location:</strong>
                        @if ($lastLocation)
                            {{ $lastLocation->lat }}, {{ $lastLocation->lng }}
                            <span class="text-muted">({{ $lastLocation->updated_at?->format('Y-m-d H:i') }})</span>
                            <div class="small">
                                <a href="https://maps.google.com/?q={{ $lastLocation->lat }},{{ $lastLocation->lng }}" target="_blank">View on map</a>
                            </div>
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Financial Breakdown</h5>
                </div>
                <div class="card-body">
                    <p><strong>Base Charge:</strong> {{ settings()->currency }} {{ number_format((float) ($parcel->base_delivery_charge ?? 0), 2) }}</p>
                    <p><strong>Receiver Markup:</strong> {{ settings()->currency }} {{ number_format((float) ($parcel->receiver_markup ?? 0), 2) }}</p>
                    <p><strong>Final Paid:</strong> {{ settings()->currency }} {{ number_format((float) ($parcel->final_paid_amount ?? 0), 2) }}</p>
                    <p><strong>Commission %:</strong> {{ number_format((float) ($parcel->commission_percent ?? 0), 2) }}%</p>
                    <p><strong>Rider Earning:</strong> {{ settings()->currency }} {{ number_format((float) ($parcel->rider_earning ?? 0), 2) }}</p>
                    <p><strong>Who Pays:</strong>
                        @if (($parcel->who_pays_either ?? null) == App\Enums\WhoPays::SENDER)
                            Sender
                        @elseif (($parcel->who_pays_either ?? null) == App\Enums\WhoPays::RECIPIENT)
                            Receiver
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Delivery Proof</h5>
                </div>
                <div class="card-body">
                    <p><strong>OTP Verified:</strong> {{ $parcel->receiver_otp_verified_at?->format('Y-m-d H:i') ?? '-' }}</p>
                    <p><strong>Proof Timestamp:</strong> {{ $parcel->delivery_proof_timestamp?->format('Y-m-d H:i') ?? '-' }}</p>
                    <p><strong>Proof Location:</strong>
                        @if ($parcel->delivery_proof_lat && $parcel->delivery_proof_lng)
                            {{ $parcel->delivery_proof_lat }}, {{ $parcel->delivery_proof_lng }}
                        @else
                            -
                        @endif
                    </p>
                    <p><strong>Proof Image:</strong>
                        @if ($parcel->delivery_proof_image_id)
                            @php
                                $proofUpload = \App\Models\Backend\Upload::find($parcel->delivery_proof_image_id);
                            @endphp
                            @if ($proofUpload)
                                <a href="{{ asset($proofUpload->original) }}" target="_blank">View</a>
                            @else
                                -
                            @endif
                        @else
                            -
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h5 class="mb-0">Dispute History</h5>
        </div>
        <div class="card-body">
            <p><strong>Disputes for this parcel:</strong> {{ $parcelDisputeCount ?? 0 }}</p>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h5 class="mb-0">Evidence Files</h5>
        </div>
        <div class="card-body">
            @if (!empty($evidenceUploads))
                <ul class="list-unstyled">
                    @foreach ($evidenceUploads as $upload)
                        <li>
                            <a href="{{ asset($upload->original) }}" target="_blank">
                                {{ basename($upload->original) }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="mb-0">No evidence uploaded.</p>
            @endif
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h5 class="mb-0">Admin Decision</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.disputes.update', $dispute->id) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-4">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="open" @selected($dispute->status === 'open')>Open</option>
                            <option value="under_review" @selected($dispute->status === 'under_review')>Under Review</option>
                            <option value="resolved" @selected($dispute->status === 'resolved')>Resolved</option>
                            <option value="rejected" @selected($dispute->status === 'rejected')>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="liability">Liability</label>
                        <select id="liability" name="liability" class="form-control">
                            <option value="">None</option>
                            <option value="rider" @selected($dispute->liability === 'rider')>Rider</option>
                            <option value="sender" @selected($dispute->liability === 'sender')>Sender</option>
                            <option value="platform" @selected($dispute->liability === 'platform')>Platform</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="refund_amount">Refund Amount</label>
                        <input type="number" step="0.01" min="0" id="refund_amount" name="refund_amount"
                            value="{{ old('refund_amount', $dispute->refund_amount) }}" class="form-control">
                    </div>
                    <!-- <div class="col-md-4 mt-3">
                        <label for="rider_liability_amount">Rider Liability Amount</label>
                        <input type="number" step="0.01" min="0" id="rider_liability_amount" name="rider_liability_amount"
                            value="{{ old('rider_liability_amount', $dispute->rider_liability_amount) }}" class="form-control">
                        <small class="text-muted d-block mt-1">Required when liability is shared.</small>
                    </div> -->
                    <div class="col-md-4 mt-3">
                        <label for="refund_status">Refund Status</label>
                        <select id="refund_status" name="refund_status" class="form-control">
                            <option value="pending" @selected($dispute->refund_status === 'pending')>Pending</option>
                            <option value="processed" @selected($dispute->refund_status === 'processed')>Processed</option>
                        </select>
                    </div>
                    <div class="col-md-4 mt-3">
                        <label for="refund_method">Refund Method</label>
                        <select id="refund_method" name="refund_method" class="form-control">
                            <option value="">Select method</option>
                            <option value="bank_transfer" @selected(old('refund_method', $dispute->refund_method) === 'bank_transfer')>Bank Transfer</option>
                            <option value="mobile_money" @selected(old('refund_method', $dispute->refund_method) === 'mobile_money')>Mobile Money</option>
                            <option value="cash" @selected(old('refund_method', $dispute->refund_method) === 'cash')>Cash</option>
                            <option value="gateway_refund" @selected(old('refund_method', $dispute->refund_method) === 'gateway_refund')>Payment Gateway Refund</option>
                            <option value="other" @selected(old('refund_method', $dispute->refund_method) === 'other')>Other</option>
                        </select>
                    </div>
                    <div class="col-md-4 mt-3">
                        <label for="refund_reference_id">Refund Reference ID</label>
                        <input type="text" id="refund_reference_id" name="refund_reference_id"
                            value="{{ old('refund_reference_id', $dispute->refund_reference_id) }}" class="form-control" placeholder="TXN12345">
                    </div>
                    <div class="col-md-4 mt-3">
                        <label for="refund_processed_at">Refund Processed At</label>
                        <input type="datetime-local" id="refund_processed_at" name="refund_processed_at"
                            value="{{ old('refund_processed_at', $dispute->refund_processed_at ? $dispute->refund_processed_at->format('Y-m-d\\TH:i') : '') }}" class="form-control">
                    </div>
                    <div class="col-12 mt-3">
                        <label for="refund_note">Refund Note</label>
                        <textarea id="refund_note" name="refund_note" rows="2" class="form-control">{{ old('refund_note', $dispute->refund_note) }}</textarea>
                    </div>
                    <div class="col-12 mt-3">
                        <label for="admin_decision">Admin Decision</label>
                        <textarea id="admin_decision" name="admin_decision" rows="3" class="form-control">{{ old('admin_decision', $dispute->admin_decision) }}</textarea>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Save Decision</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
