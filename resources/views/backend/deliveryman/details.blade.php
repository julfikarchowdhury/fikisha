@extends('backend.partials.master')
@section('title')
    {{ __('deliveryman.title') }} {{ __('levels.details') }}
@endsection
@section('maincontent')
    <!-- wrapper  -->
    <div class="container-fluid  dashboard-content">
        <!-- pageheader -->
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                        class="breadcrumb-link">{{ __('levels.dashboard') }}</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('deliveryman.index') }}"
                                        class="breadcrumb-link">{{ __('deliveryman.title') }}</a></li>
                                <li class="breadcrumb-item"><a href=""
                                        class="breadcrumb-link active">{{ __('levels.details') }}</a></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>


        <!-- end pageheader -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">Rider Summary</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3"><strong>{{ __('levels.name') }}:</strong> {{ @$deliveryman->user->name }}</div>
                            <div class="mb-3"><strong>{{ __('levels.email') }}:</strong> {{ @$deliveryman->user->email }}</div>
                            <div class="mb-3"><strong>{{ __('levels.phone') }}:</strong> {{ @$deliveryman->user->mobile }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3"><strong>Rider Status:</strong> {{ $deliveryman->rider_status_label ?? 'Approved' }}</div>
                            <div class="mb-3"><strong>{{ __('levels.status') }}:</strong> {!! @$deliveryman->my_status !!}</div>
                            <div class="mb-3"><strong>{{ __('levels.verification_status') }}:</strong> {!! @$deliveryman->user->my_verification_status !!}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3"><strong>{{ __('levels.document_status') }}:</strong> {!! @$deliveryman->user->my_document_status !!}</div>
                            <div class="mb-3"><strong>{{ __('levels.residence_address') }}:</strong> {{ @$deliveryman->residence_address }}</div>
                            <div class="mb-3"><strong>{{ __('levels.nid_number') }}:</strong> {{ @$deliveryman->user->nid_number }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">Vehicle & License</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3"><strong>{{ __('levels.vehicle') ?? 'Vehicle' }}:</strong> {{ @$deliveryman->vehicle_type ?? '-' }}</div>
                            <div class="mb-3"><strong>{{ __('levels.registration_no') }}:</strong> {{ @$deliveryman->registration_no }}</div>
                            <div class="mb-3"><strong>{{ __('levels.chassis_no') }}:</strong> {{ @$deliveryman->chassis_no }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3"><strong>{{ __('levels.colour') }}:</strong> {{ @$deliveryman->colour }}</div>
                            <div class="mb-3"><strong>{{ __('levels.driving_license') }}:</strong>
                                <a href="{{ @$deliveryman->driving_license_image }}" class="text-primary">Download</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3"><strong>{{ __('levels.insurance_no') }}:</strong> {{ @$deliveryman->insurance_no }}</div>
                            <div class="mb-3"><strong>{{ __('levels.insurance_expiry_date') }}:</strong> {{ @$deliveryman->insurance_expiry_date }}</div>
                            <div class="mb-3"><strong>{{ __('levels.insurance_company') }}:</strong> {{ @$deliveryman->insurance_company }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center gap-2 flex-wrap">
                    <h4 class="mb-0" style="font-size: 18px; font-weight: 500;">KYC Documents</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div><strong>{{ __('levels.front_side_scan') }}</strong></div>
                            <a href="{{ data_get($deliveryman->allimage, 'front_side_scan') }}" class="text-primary">Download</a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div><strong>{{ __('levels.back_side_scan') }}</strong></div>
                            <a href="{{ data_get($deliveryman->allimage, 'back_side_scan') }}" class="text-primary">Download</a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div><strong>{{ __('levels.regis_front_scan') }}</strong></div>
                            <a href="{{ data_get($deliveryman->allimage, 'regis_front_scan') }}" class="text-primary">Download</a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div><strong>{{ __('levels.regis_back_scan') }}</strong></div>
                            <a href="{{ data_get($deliveryman->allimage, 'regis_back_scan') }}" class="text-primary">Download</a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div><strong>{{ __('levels.inspctn_check_scan') }}</strong></div>
                            <a href="{{ data_get($deliveryman->allimage, 'inspctn_check_scan') }}" class="text-primary">Download</a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div><strong>{{ __('levels.insurance_crtfy_scan') }}</strong></div>
                            <a href="{{ data_get($deliveryman->allimage, 'insurance_crtfy_scan') }}" class="text-primary">Download</a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div><strong>{{ __('levels.tech_c_scan') }}</strong></div>
                            <a href="{{ data_get($deliveryman->allimage, 'tech_c_scan') }}" class="text-primary">Download</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- end wrapper  -->
@endsection
