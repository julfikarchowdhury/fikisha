@extends('backend.partials.master')
@section('title')
    {{ __('parcel.analytics') }}
@endsection
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
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"
                                        class="breadcrumb-link">{{ __('levels.dashboard') }}</a></li>
                                <li class="breadcrumb-item"><a href="javascript:void(0)"
                                        class="breadcrumb-link">{{ __('parcel.analytics') }}</a></li>
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

                <div class="row p-0 mb-3">
                    <div class="col-12 col-md-6">
                        <p class="h3 d-inline">{{ __('parcel.analytics') }}</p>
                    </div>
                    <div class="col-12 col-md-6 text-right">
                        <form action="{{ route('merchant.panel.analytics') }}" method="get"
                            class="d-flex justify-content-end">
                            <input type="text" autocomplete="off" id="date" name="filter_date"
                                class=" form-control  py-1 w-50 date_range_picker group-input"
                                value="{{ isset($request->filter_date) ? $request->filter_date : old('filter_date') }}"
                                placeholder="{{ __('merchantPlaceholder.date') }}">
                            <button type="submit" class="btn btn-sm btn-primary group-btn"
                                style="margin-left: -5px!important"><i class="fa fa-search"></i>
                                {{ __('levels.filter') }}</button>
                        </form>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 ">
                        <div class="card border-3 border-top border-top-primary analytics-col-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="fs-5">{{ __('parcel.title') }} </span>
                                        <h1 class="mb-0">{{ @$total_orders }}</h1>
                                    </div>
                                    <div>
                                        <span class="rounded bg-outline-primary d-inline-block orders-box">
                                            <i class="fa-solid fa-box fs-1"></i>
                                        </span>
                                    </div>
                                </div>
                                <hr style="border-top: 2px solid rgb(0 0 0 / 68%); " />
                                <div class="row">
                                    <div class="col-lg-6">
                                        <a href="#">
                                            <div
                                                class="d-flex justify-content-between align-items-center analytics-order-item">
                                                <div style="line-height: 2">
                                                    <div>
                                                        <i class="fa fa-circle text-success"></i>
                                                        {{ __('parcel.finished') }}
                                                    </div>
                                                    <div class="pl-3">
                                                        <b>{{ @$total_delivered }} {{ __('parcel.title') }}</b>
                                                    </div>
                                                </div>
                                                <div>
                                                    <i class="fa fa-angle-right fs-4"></i>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-lg-6">
                                        <a href="#">
                                            <div
                                                class="d-flex justify-content-between align-items-center analytics-order-item">
                                                <div style="line-height: 2">
                                                    <div>
                                                        <i class="fa fa-circle text-danger"></i>
                                                        {{ __('parcel.cancelled') }}
                                                    </div>
                                                    <div class="pl-3">
                                                        <b>{{ @$total_cancelled }} {{ __('parcel.title') }}</b>
                                                    </div>
                                                </div>
                                                <div>
                                                    <i class="fa fa-angle-right fs-4"></i>
                                                </div>
                                            </div>
                                        </a>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 ">
                        <div class="card border-3 border-top border-top-primary analytics-col-card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="fs-5">{{ __('parcel.distance') }} </span>
                                        <h1 class="mb-0">{{ @$total_distance }} KM</h1>
                                    </div>
                                    <div>
                                        <span class="rounded bg-outline-primary d-inline-block orders-box">
                                            <i class="fa-solid fa-route fs-1"></i>
                                        </span>
                                    </div>
                                </div>
                                <hr style="border-top: 2px solid rgb(0 0 0 / 68%); " />
                                <div class="row">
                                    <div class="col-lg-6">
                                        <a href="#">
                                            <div
                                                class="d-flex justify-content-between align-items-center analytics-order-item">
                                                <div style="line-height: 2">
                                                    <div>
                                                        <i class="fa fa-circle text-success"></i> {{ __('parcel.inside') }}
                                                    </div>
                                                    <div class="pl-3">
                                                        <b>{{ @$total_inside_distance }} KM</b>
                                                    </div>
                                                </div>
                                                <div>
                                                    <i class="fa fa-angle-right fs-4"></i>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-lg-6">
                                        <a href="#">
                                            <div
                                                class="d-flex justify-content-between align-items-center analytics-order-item">
                                                <div style="line-height: 2">
                                                    <div>
                                                        <i class="fa fa-circle text-danger"></i> {{ __('parcel.outside') }}
                                                    </div>
                                                    <div class="pl-3">
                                                        <b>{{ @$total_outside_distance }} KM</b>
                                                    </div>
                                                </div>
                                                <div>
                                                    <i class="fa fa-angle-right fs-4"></i>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-6 ">
                        <div class="card border-3 border-top border-top-primary analytics-col-card h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="fs-5">{{ __('expense.title') }} </span>
                                        <h1 class="mb-0">{{ settings()->currency }} {{ @$total_expense }}</h1>
                                    </div>
                                    <div>
                                        <span class="rounded bg-outline-primary d-inline-block orders-box">
                                            <i class="fa-solid fa-wallet fs-1"></i>
                                        </span>
                                    </div>
                                </div>
                                <hr style="border-top: 2px solid rgb(0 0 0 / 68%); " />
                                <div class="row">
                                    <div class="col-lg-6">
                                        <a href="#">
                                            <div
                                                class="d-flex justify-content-between align-items-center analytics-order-item">
                                                <div style="line-height: 2">
                                                    <div>
                                                        <i class="fa fa-circle text-success"></i> {{ __('parcel.inside') }}
                                                    </div>
                                                    <div class="pl-3">
                                                        <b>{{ settings()->currency }} {{ @$total_inside_expense }}</b>
                                                    </div>
                                                </div>
                                                <div>
                                                    <i class="fa fa-angle-right fs-4"></i>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-lg-6">
                                        <a href="#">
                                            <div
                                                class="d-flex justify-content-between align-items-center analytics-order-item">
                                                <div style="line-height: 2">
                                                    <div>
                                                        <i class="fa fa-circle text-danger"></i>
                                                        {{ __('parcel.outside') }}
                                                    </div>
                                                    <div class="pl-3">
                                                        <b> {{ settings()->currency }} {{ @$total_outside_distance }}</b>
                                                    </div>
                                                </div>
                                                <div>
                                                    <i class="fa fa-angle-right fs-4"></i>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6  ">
                        <div class="card h-100">
                            <div class="card-body" width="100%" height="200px">
                                <div class="apexcharts" id="apexparcelspiechart" style="padding-bottom:0px"></div>
                                <div class="pie-chart-text" style="line-height: 2">
                                    <div>
                                        <i class="fa fa-circle text-danger"></i>
                                        {{ __('parcel.in-house_couriers') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="charts mt-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center my-2">
                                <div>
                                    <h2 class="mb-0">{{ __('parcel.title') }} </h2>
                                </div>
                                <div>
                                    <ul class="nav nav-pills chart-tabs" id="pills-tab" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="day-tab" data-bs-toggle="pill"
                                                data-bs-target="#day" type="button" role="tab" aria-controls="day"
                                                aria-selected="false">Day</button>
                                        </li>

                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link " id="month-tab" data-bs-toggle="pill"
                                                data-bs-target="#month" type="button" role="tab"
                                                aria-controls="month" aria-selected="true">Month</button>
                                        </li>

                                    </ul>
                                </div>
                            </div>
                            <div id="chart"></div>
                        </div>
                    </div>
                </div>



            </div>
            <!-- end data table  -->
        </div>
    </div>
    <!-- end wrapper  -->
@endsection()


@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
@endpush
@push('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="{{ static_asset('backend/js/date-range-picker/date-range-picker-custom.js') }}">
    </script>
    <script type="text/javascript" src="{{ static_asset('backend/js/charts/apexcharts.js') }}"></script>
    <script type="text/javascript">
        //apex charts parcels piecharts
        var options = {
            series: [{{ @$total_orders }}, {{ @$total_delivered }}, {{ @$total_cancelled }}],
            chart: {
                width: '100%',
                height: 400,
                type: 'pie',
            },
            colors: ['#5b0b97', '#009688', '#ff407b'],
            labels: ["{{ __('dashboard.total_orders') }}", "{{ __('dashboard.total_delivered') }}",
                "{{ __('dashboard.total_cancelled') }}"
            ],
            title: {
                text: 'Order Distribution',
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };
        var chart = new ApexCharts(document.querySelector("#apexparcelspiechart"), options);
        chart.render();
    </script>




    <script>
        var options = {
            series: [{
                    name: 'All Order',
                    data: {{ json_encode(array_values($seven_days_order['all_order'])) }}
                },
                {
                    name: 'Pending',
                    data: {{ json_encode(array_values($seven_days_order['pending_order'])) }}
                },
                {
                    name: 'Completed',
                    data: {{ json_encode(array_values($seven_days_order['complete_order'])) }}
                },
                {
                    name: 'Cancelled',
                    data: {{ json_encode(array_values($seven_days_order['cancel_order'])) }}
                }
            ],

            colors: ['#5b0b97', '#FF9800', '#009688', '#ff407b'],

            chart: {
                height: 350,
                type: 'area'
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },

            xaxis: {
                type: 'date',
                categories: [
                    @foreach ($seven_days_order['all_order'] as $s_day_key => $s_day)
                        "{{ $s_day_key }}",
                    @endforeach
                ]
            },
            tooltip: {
                x: {
                    format: 'dd/MM/yy HH:mm'
                },
            },
        };

        var chart2 = new ApexCharts(document.querySelector("#chart"), options);
        chart2.render();

        $('#day-tab').click(function() {
            chart2.updateSeries([{
                    name: 'All Order',
                    data: {{ json_encode(array_values($seven_days_order['all_order'])) }}
                },
                {
                    name: 'Pending',
                    data: {{ json_encode(array_values($seven_days_order['pending_order'])) }}
                },
                {
                    name: 'Completed',
                    data: {{ json_encode(array_values($seven_days_order['complete_order'])) }}
                },
                {
                    name: 'Cancelled',
                    data: {{ json_encode(array_values($seven_days_order['cancel_order'])) }}
                }
            ]);
            chart2.updateOptions({
                xaxis: {
                    type: 'date',
                    categories: [
                        @foreach ($seven_days_order['all_order'] as $s_day_key => $s_day)
                            "{{ $s_day_key }}",
                        @endforeach
                    ]
                },
            })
        });



        $('#month-tab').click(function() {
            chart2.updateSeries([{
                    name: 'All Order',
                    data: {{ json_encode(array_values($current_month_order['all_order'])) }}
                }, {
                    name: 'Pending',
                    data: {{ json_encode(array_values($current_month_order['pending_order'])) }}
                },
                {
                    name: 'Completed',
                    data: {{ json_encode(array_values($current_month_order['complete_order'])) }}
                },
                {
                    name: 'Cancelled',
                    data: {{ json_encode(array_values($current_month_order['cancel_order'])) }}
                }
            ]);
            chart2.updateOptions({
                xaxis: {
                    type: 'date',
                    categories: [
                        @foreach ($current_month_order['all_order'] as $m_day_key => $m_day)
                            "{{ $m_day_key }}",
                        @endforeach
                    ]
                },
            })
        });
    </script>
@endpush
