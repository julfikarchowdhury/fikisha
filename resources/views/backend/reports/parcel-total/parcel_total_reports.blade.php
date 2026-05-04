@extends('backend.partials.master')
@section('title','Total Revenue | List')
@section('maincontent')
<!-- wrapper  -->
<div class="container-fluid  dashboard-content">
    <!-- pageheader -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="page-header">
                <div class="page-breadcrumb">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{route('dashboard.index')}}" class="breadcrumb-link">{{ __('reports.dashboard') }}</a></li>
                            <li class="breadcrumb-item"><a href="#" class="breadcrumb-link">{{ __('reports.title') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{route('parcel.total.summery.index') }}" class="breadcrumb-link">Total Revenue</a></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- end pageheader -->
    <div class="row">
        <!-- data table  -->
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('parcel.filter.total.summery')}}"  method="GET">
                        @csrf
                        <div class="row">
                            <div class="form-group col-xl-3 col-md-4  col-sm-6">
                                <label for="parcel_date">{{ __('parcel.date') }}</label>
                                <input type="text" autocomplete="off" id="date" name="parcel_date" class="form-control date_range_picker" value="{{ old('parcel_date',$request->parcel_date) }}">
                                @error('parcel_date')
                                    <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="form-group col-xl-3 col-md-4 col-sm-6">
                                <label for="parcelMerchantid">{{ __('parcel.merchant') }}</label>
                                <select style="width: 100%" id="parcelMerchantid"  name="parcel_merchant_id" class="form-control @error('parcel_merchant_id') is-invalid @enderror" data-url="{{ route('parcel.merchant.shops') }}">
                                    <option value=""> {{ __('menus.select') }} {{ __('merchant.title') }}</option>
                                </select>
                                @error('parcel_merchant_id')
                                <small class="text-danger mt-2">{{ $message }}</small>
                                @enderror
                            </div>
                            <div class="col-lg-3 col-xl-3 col-md-4 col-sm-6">
                                <div class="form-group d-inline-block pt-1">
                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 pt-4 d-flex justify-content pl-0">
                                        <button type="submit" class="btn btn-space btn-primary"><i class="fa fa-filter"></i> {{ __('levels.filter') }}</button>
                                        <a href="{{ route('parcel.total.summery.index') }}" class="btn btn-space btn-secondary"><i class="fa fa-eraser"></i> {{ __('levels.clear') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row">
                @if(!blank($summary))
                    <div class="col-xl-6 col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Marketplace Totals</h5>
                            </div>
                            <div class="card-body p-2">
                                <ul class="list-group m-2">
                                    <li class="list-group-item profile-list-group-item">
                                        <span class="float-left font-weight-bold">Total Delivered</span>
                                        <span class="float-right">{{ $summary['total_delivered'] }}</span>
                                    </li>
                                    <li class="list-group-item profile-list-group-item">
                                        <span class="float-left font-weight-bold">Base Charge</span>
                                        <span class="float-right">{{ settings()->currency }} {{ number_format($summary['total_base_charge'], 2) }}</span>
                                    </li>
                                    <li class="list-group-item profile-list-group-item">
                                        <span class="float-left font-weight-bold">Receiver Markup</span>
                                        <span class="float-right">{{ settings()->currency }} {{ number_format($summary['total_receiver_markup'], 2) }}</span>
                                    </li>
                                    <li class="list-group-item profile-list-group-item">
                                        <span class="float-left font-weight-bold">Final Paid</span>
                                        <span class="float-right">{{ settings()->currency }} {{ number_format($summary['total_final_paid'], 2) }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Earnings Breakdown</h5>
                            </div>
                            <div class="card-body p-2">
                                <ul class="list-group m-2">
                                    <li class="list-group-item profile-list-group-item">
                                        <span class="float-left font-weight-bold">Commission</span>
                                        <span class="float-right">{{ settings()->currency }} {{ number_format($summary['total_commission'], 2) }}</span>
                                    </li>
                                    <li class="list-group-item profile-list-group-item">
                                        <span class="float-left font-weight-bold">Rider Earning</span>
                                        <span class="float-right">{{ settings()->currency }} {{ number_format($summary['total_rider_earning'], 2) }}</span>
                                    </li>
                                    <li class="list-group-item profile-list-group-item">
                                        <span class="float-left font-weight-bold">Platform Earning</span>
                                        <span class="float-right">{{ settings()->currency }} {{ number_format($summary['total_platform_earning'], 2) }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Rider Earnings Overview</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Rider</th>
                                                <th>Total Deliveries</th>
                                                <th>Total Earned</th>
                                                <th>Total Commission</th>
                                                <th>Total Platform Earning</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($riderSummaries as $summaryRow)
                                                <tr>
                                                    <td>{{ $summaryRow->rider_name ?? 'N/A' }}</td>
                                                    <td>{{ $summaryRow->total_deliveries }}</td>
                                                    <td>{{ settings()->currency }} {{ number_format((float) $summaryRow->total_earned, 2) }}</td>
                                                    <td>{{ settings()->currency }} {{ number_format((float) $summaryRow->total_commission, 2) }}</td>
                                                    <td>{{ settings()->currency }} {{ number_format((float) ($summaryRow->total_platform_earning ?? 0), 2) }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">No rider data available.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center text-muted">
                                No data available. Please apply a filter.
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- end wrapper  -->


@endsection()



<!-- css  -->
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        #selectAssignType .select2-container .select2-selection--single {
        height: 32px !important;
    }
    </style>
@endpush
<!-- js  -->
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="{{ static_asset('backend/js/date-range-picker/date-range-picker-custom.js') }}"></script>
    <script>
    var merchantUrl = '{{ route('parcel.merchant.get') }}';
        var merchantID = '{{ $request->parcel_merchant_id }}';
        var deliveryManID = '{{ $request->parcel_deliveryman_id }}';
        var pickupManID = '{{ $request->parcel_pickupman_id }}';
        var dateParcel = '{{ $request->parcel_date }}';
    </script>
    <script src="{{ static_asset('backend/js/parcel/filter.js') }}"></script>

    <script src="{{ static_asset('backend/js/reports/print.js') }}"></script>
    <script src="{{ static_asset('backend/js/reports/jquery.table2excel.min.js') }}"></script>
    <script src="{{ static_asset('backend/js/reports/reports.js') }}"></script>

 @endpush



