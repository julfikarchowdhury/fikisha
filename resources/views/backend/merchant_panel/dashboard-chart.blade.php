<script type="text/javascript">
    //apext charts parcels
    var options = {
    series: [
         {
            name: '{{ __("dashboard.total")}}',
            type: 'area',
            data: [
                @foreach($dates as $key => $date)
                  {{ $totals[$key] }},
                @endforeach
            ]
        },
        {
            name: '{{  __("dashboard.unassigned") }}',
            type: 'area',
            data: [
                @foreach($dates as $key => $date)
                  {{ $pendings[$key] }},
                @endforeach
            ]
        },
        {
            name: '{{  __("dashboard.assigned") }}',
            type: 'area',
            data: [
                @foreach($dates as $key => $date)
                  {{ $assigned_data[$key] }},
                @endforeach
            ]
        },
        {
            name: '{{  __("dashboard.processing") }}',
            type: 'area',
            data: [
                @foreach($dates as $key => $date)
                  {{ $processings[$key] }},
                @endforeach
            ]
        },
        {
            name: '{{  __("dashboard.deliver") }}',
            type: 'area',
            data: [
                @foreach($dates as $key => $date)
                  {{ $delivers[$key] }},
                @endforeach
            ]
        },
        {
            name: '{{  __("dashboard.failure") }}',
            type: 'area',
            data: [
                @foreach($dates as $key => $date)
                  {{ $failures[$key] }},
                @endforeach
            ]
        }
    ],
    // colors:['#2E93fA', '#ff407b'],
    chart: {
        height: 600,
        width: '100%',
        type: 'area',
    },
    stroke: {
        curve: 'smooth'
    },
    colors:['#2E93fA', '#ff407b','#009688','#2ec551','#2ec551','#ff407b'],
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.5,
            opacityTo: 0.7,
            stops: [0, 100]
        }
    },
    title: {
        text: ' {{ __('parcel.title') }}',
    },
    labels: [ @foreach($dates as $key => $date)
                  '{{$date }}',
                @endforeach],
    markers: {
        size: 0
    },

    tooltip: {
        shared: true,
        intersect: false,
        y: {
            formatter: function (y) {
                if (typeof y !== "undefined") {
                    return y.toFixed(0);
                }
                return y;
            }
        }
    }
};
var chart = new ApexCharts(document.querySelector("#apexparcels"), options);
chart.render();

 //apex charts parcelspiecharts
 var options = {
        series: [{{ $piedata['total_parcels'] }},{{ $piedata['total_pending'] }},{{ $piedata['total_assigned'] }},{{ $piedata['total_processing'] }},{{ $piedata['total_delivered'] }},{{ $piedata['total_failure'] }}],
            chart: {
            width: '100%',
            height: 700,
            type: 'pie',
        },
        colors:[ '#2E93fA','#ff407b','#2ec551','#009688','#2ec551','#ff407b'],
        labels: ["{{ __('dashboard.total') }}","{{ __('dashboard.unassigned') }}","{{ __('dashboard.assigned') }}","{{ __('dashboard.processing') }}","{{ __('dashboard.deliver') }}","{{ __('dashboard.failure') }}"],
        title: {
        text: ' {{ __("parcel.title") }}',
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