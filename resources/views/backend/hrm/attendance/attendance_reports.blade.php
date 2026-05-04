@extends('backend.partials.master')
@section('title')
    {{ __('reports.attendance_reports') }}
@endsection
@section('maincontent')
    <div class="container-fluid  dashboard-content">
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="page-header">
                    <div class="page-breadcrumb">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#"
                                        class="breadcrumb-link">{{ __('reports.title') }}</a></li>
                                <li class="breadcrumb-item"><a href="javascript:void(0)"
                                        class="breadcrumb-link">{{ __('reports.attendance_reports') }}</a></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="card">
                    <div class="card-body">
                       
                        <form action="{{ route('hrm.attendance.reports') }}" method="GET">
                            <input type="hidden" value="filter" name="filter"/>
                            <div class="row">
                                <div class="form-group col-12  col-xl-3 col-lg-4 col-md-4 col-sm-6">
                                    <label for="date">{{ __('parcel.date') }}</label>
                                    <input type="text" autocomplete="off" id="date" name="date"
                                        class="form-control date_range_picker" value="{{ old('date', $request->date) }}">
                                </div>
                                <div class="form-group col-12  col-xl-3 col-lg-4 col-md-4 col-sm-6">
                                    <label for="user_id">{{ __('levels.user') }}</label><span
                                        class="text-danger ms-2">*</span>
                                    <select style="width: 100%" id="user_id" name="user_id"
                                        class="form-control select2 @error('user_id') is-invalid @enderror">
                                        <option value=""> {{ __('Select User') }}</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                @if (old('user_id', $request->user_id) == $user->id) selected @endif> {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group col-12 col-xl-3 col-lg-4 col-md-4 col-sm-6 pt-1">
                                    <div
                                        class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 pt-4 d-flex justify-content pl-0">
                                        <button type="submit" class="btn btn-space btn-primary"><i
                                                class="fa fa-filter"></i> {{ __('levels.filter') }}</button>
                                        <a href="{{ route('hrm.attendance.reports') }}"
                                            class="btn btn-space btn-secondary"><i class="fa fa-eraser"></i>
                                            {{ __('levels.clear') }}</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                @if (isset($attendances))
                    <div class="card " id="attendance_report">
                        <div class="card-header">
                            <div class="print-button text-end">
                                <button class="btn btn-secondary" type="button"
                                    onclick="printOnlyDiv('attendance_report')"><i class="fa fa-print"></i></button>
                            </div>
                            <div class="row py-3 align-items-center">
                                <div class="col-4">
                                    <span>{{ __('levels.name') }}: {{ $user->name }}</span><br />
                                    <span>{{ __('levels.email') }}: {{ $user->email }}</span><br />
                                    <span>{{ __('levels.phone') }}: {{ $user->mobile }}</span>
                                </div>
                                <div class="col-4">
                                    <div class="text-center d-inline-block">
                                        <h2>{{ settings()->name }}</h2>
                                        <p class="mb-2">{{ settings()->address }}</p>
                                        <h3 class="mb-2"> {{ __('reports.attendance_report') }}</h3>
                                        @if ($request->date)
                                            <h4>{{ __('reports.date') }}: {{ $request->date }}</h4>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-4">
                                    <table class="table border">
                                        <tbody>

                                            <tr>
                                                <td>{{ __('reports.total_present') }}</td>
                                                <td>{{ @$attendances->count() }} {{ __('reports.days') }}</td>
                                            </tr>
                                            <tr>
                                                <td>{{ __('reports.total_over_time') }}</td>
                                                <td>
                                                    @php
                                                        $hours = (int) ($attendances->sum('over_stay_time') / 60);
                                                        $HoursMinutes = $hours * 60;
                                                        $OMinutes = $attendances->sum('over_stay_time') - $HoursMinutes;
                                                        $totalOverTime = $hours . 'H ' . $OMinutes . 'M';
                                                    @endphp
                                                    {{ @$totalOverTime }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div>
                                <table class="table" style="width:100%">
                                    @php $i=1; @endphp
                                    <thead>

                                        <tr>
                                            <th>{{ __('#') }}</th>
                                            <th>{{ __('reports.date') }}</th>
                                            <th>{{ __('reports.check_in') }}</th>
                                            <th>{{ __('reports.in_ip_address') }}</th>
                                            <th>{{ __('reports.check_out') }}</th>
                                            <th>{{ __('reports.out_ip_address') }}</th>
                                            <th>{{ __('reports.total_work_time') }}</th>
                                            <th>{{ __('reports.over_time') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($attendances as $key => $attendance)
                                            <tr>
                                                <th>{{ $i++ }}</th>
                                                <td> {{ @$attendance->date }} </td>
                                                <td>{{ \Carbon\Carbon::parse($attendance->check_in)->format('H:i A') }}
                                                </td>
                                                <td>{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i A') : '' }}
                                                </td>
                                                <td>{{ @$attendance->in_ip_address }}</td>
                                                <td>{{ @$attendance->out_ip_address }}</td>
                                                <td>{{ @$attendance->staytime }}</td>
                                                <td>{{ @$attendance->overtime }}</td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

<!-- css  -->
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        #selectAssignType .select2-container .select2-selection--single {
            height: 32px !important;
        } 
        @media print {
            body {
                font-size: 18px !important;
                background: white !important;
                color: black !important
            }

            .card {
                border: none !important;
            }

            .card-header {
                border: none !important;
            }

            .print-button {
                display: none !important;
            }
        }
    </style>
@endpush
<!-- js  -->
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery.print/1.6.2/jQuery.print.min.js"></script>
    <script>
        var dateParcel = '{{ $request->parcel_date }}';

        function printOnlyDiv(divName) {
            var div_id = document.getElementById(divName);
            $.print(div_id);
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="{{ static_asset('backend/js/date-range-picker/date-range-picker-custom.js') }}">
    </script>
@endpush
