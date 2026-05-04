@extends('backend.partials.master')
@section('title')
Rider KYC Review
@endsection
@section('maincontent')
<div class="container-fluid dashboard-content">
    <div class="row">
        <div class="col-12">
            <div class="page-header">
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard.index')}}" class="breadcrumb-link">{{ __('levels.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('deliveryman.index') }}" class="breadcrumb-link">Riders</a></li>
                            <li class="breadcrumb-item active" aria-current="page">KYC Review</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('deliveryman.kyc.index') }}" method="GET">
                <div class="row">
                    <div class="form-group col-12 col-md-4">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control select2">
                            <option value="">All</option>
                            <option value="{{ \App\Enums\RiderStatus::PENDING_KYC }}" @selected($status == \App\Enums\RiderStatus::PENDING_KYC)>Pending KYC</option>
                            <option value="{{ \App\Enums\RiderStatus::UNDER_REVIEW }}" @selected($status == \App\Enums\RiderStatus::UNDER_REVIEW)>Under Review</option>
                        </select>
                    </div>
                    <div class="form-group col-12 col-md-3 pt-4">
                        <button type="submit" class="btn btn-space btn-primary"><i class="fa fa-filter"></i> {{ __('levels.filter') }}</button>
                        <a href="{{ route('deliveryman.kyc.index') }}" class="btn btn-space btn-secondary"><i class="fa fa-eraser"></i> {{ __('levels.clear') }}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
            <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">Rider KYC Review</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table" style="width:100%">
                    <thead>
                        <tr>
                            <th>{{ __('levels.id') }}</th>
                            <th>{{ __('levels.user') }}</th>
                            <th>{{ __('levels.phone') }}</th>
                            <th>Rider Status</th>
                            <th>KYC Submitted</th>
                            <th>Documents</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!blank($deliveryMans))
                        @php $i=1; @endphp
                        @foreach($deliveryMans as $deliveryman)
                        <tr>
                            <td>{{$i++}}</td>
                            <td>
                                <strong>{{$deliveryman->user->name}}</strong><br />
                                <small>{{ $deliveryman->user->email }}</small>
                            </td>
                            <td>{{ $deliveryman->user->mobile }}</td>
                            <td>{{ $deliveryman->rider_status_label ?? 'Approved' }}</td>
                            <td>{{ $deliveryman->kyc_submitted_at ? \Illuminate\Support\Carbon::parse($deliveryman->kyc_submitted_at)->toDateTimeString() : '-' }}</td>
                            <td>
                                @php
                                    $docs = [
                                        'Driving License' => $deliveryman->driving_license_image,
                                        'NID Front' => data_get($deliveryman->allimage, 'front_side_scan'),
                                        'NID Back' => data_get($deliveryman->allimage, 'back_side_scan'),
                                        'Reg. Front' => data_get($deliveryman->allimage, 'regis_front_scan'),
                                        'Reg. Back' => data_get($deliveryman->allimage, 'regis_back_scan'),
                                        'Inspection' => data_get($deliveryman->allimage, 'inspctn_check_scan'),
                                        'Insurance' => data_get($deliveryman->allimage, 'insurance_crtfy_scan'),
                                        'Tech Control' => data_get($deliveryman->allimage, 'tech_c_scan'),
                                    ];
                                @endphp
                                <button type="button"
                                    class="btn btn-sm btn-outline-primary kyc-docs-btn"
                                    data-toggle="modal"
                                    data-target="#kycDocsModal"
                                    data-name="{{ $deliveryman->user->name }}"
                                    data-docs='@json($docs)'>
                                    View Docs
                                </button>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <form action="{{ route('deliveryman.kyc.approve',$deliveryman->id) }}" method="POST" class="mr-1">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#reuploadKycModal" data-id="{{ $deliveryman->id }}">
                                        Request Re-upload
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectKycModal" data-id="{{ $deliveryman->id }}">
                                        Reject
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pb-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
            <span>{{ $deliveryMans->links() }}</span>
        </div>
    </div>
</div>

<div class="modal fade" id="kycDocsModal" tabindex="-1" role="dialog" aria-labelledby="kycDocsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="kycDocsModalLabel">KYC Documents</h5>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="kycZoomOut">-</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" id="kycZoomIn">+</button>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="kycDocsContainer" class="row"></div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectKycModal" tabindex="-1" role="dialog" aria-labelledby="rejectKycModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" id="rejectKycForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectKycModalLabel">Reject Rider</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="note">Rejection Note</label>
                        <textarea class="form-control" name="note" id="note" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="reuploadKycModal" tabindex="-1" role="dialog" aria-labelledby="reuploadKycModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" id="reuploadKycForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reuploadKycModalLabel">Request Re-upload</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="reupload_note">Re-upload Note</label>
                        <textarea class="form-control" name="note" id="reupload_note" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Request Re-upload</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
            $(".select2").select2();
        });

    $('#rejectKycModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var riderId = button.data('id');
        var form = $('#rejectKycForm');
        form.attr('action', "{{ route('deliveryman.kyc.reject', ':id') }}".replace(':id', riderId));
        form.find('#note').val('');
    });

    $('#reuploadKycModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var riderId = button.data('id');
        var form = $('#reuploadKycForm');
        form.attr('action', "{{ route('deliveryman.kyc.reupload', ':id') }}".replace(':id', riderId));
        form.find('#reupload_note').val('');
    });

    var kycZoom = 1;
    $('#kycDocsModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var name = button.data('name');
        var docs = button.attr('data-docs');
        var docsJson = docs ? JSON.parse(docs) : {};
        $('#kycDocsModalLabel').text('KYC Documents - ' + name);
        var container = $('#kycDocsContainer');
        container.empty();
        kycZoom = 1;

        Object.keys(docsJson).forEach(function (label) {
            var url = docsJson[label];
            var isBlank = !url || url.indexOf('blank-image.jpg') !== -1 || url.indexOf('default/user.png') !== -1;
            var col = $('<div class="col-md-6 mb-3"></div>');
            var card = $('<div class="card"></div>');
            var body = $('<div class="card-body"></div>');
            body.append('<h6 class="mb-2">' + label + '</h6>');

            if (isBlank) {
                body.append('<div class="text-muted">Not provided</div>');
            } else if (url.toLowerCase().indexOf('.pdf') !== -1) {
                body.append('<iframe src="' + url + '" style="width:100%;height:320px;border:1px solid #eee;"></iframe>');
                body.append('<div class="mt-2"><a class="btn btn-sm btn-outline-primary" href="' + url + '" target="_blank">Open</a> <a class="btn btn-sm btn-outline-secondary" href="' + url + '" download>Download</a></div>');
            } else {
                body.append('<div class="kyc-img-wrap" style="overflow:auto;"><img src="' + url + '" class="img-fluid kyc-doc-img" style="transform:scale(1);transform-origin:top left;" /></div>');
                body.append('<div class="mt-2"><a class="btn btn-sm btn-outline-secondary" href="' + url + '" download>Download</a></div>');
            }

            card.append(body);
            col.append(card);
            container.append(col);
        });
    });

    $('#kycZoomIn').on('click', function () {
        kycZoom = Math.min(3, kycZoom + 0.25);
        $('.kyc-doc-img').css('transform', 'scale(' + kycZoom + ')');
    });

    $('#kycZoomOut').on('click', function () {
        kycZoom = Math.max(0.5, kycZoom - 0.25);
        $('.kyc-doc-img').css('transform', 'scale(' + kycZoom + ')');
    });
</script>
@endpush
