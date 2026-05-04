
<script type="text/javascript">
            //apext charts //parcel reports
            var options = {
            series: [{
                name: 'New Parcels',
                type: 'area',
                data: [
                    @foreach($data['incomeDates'] as $date)
                                      {{  dayNewParcelCount($date) }},
                    @endforeach
                ]
            }, {
                name: 'Processing Parcels',
                type: 'area',
                data: [
                    @foreach($data['incomeDates'] as $date)
                              {{  dayProcessingParcelCount($date) }},
                    @endforeach
                ]
            }, {
                name: 'Delivered Parcels',
                type: 'area',

                data: [
                    @foreach($data['expenseDates'] as $date)
                        {{  dayDeliveredParcelCount($date) }},
                    @endforeach
                ]
            }],
            colors:['#2E93fA', '#ffc107', '#28a745'],
            chart: {
                height: 450,
                type: 'area',
            },
            stroke: {
                curve: 'smooth'
            },
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
                text: 'Parcel Reports',
            },
            labels: [@foreach($data['expenseDates'] as $date)
                           '{{ $date }}',
                    @endforeach],
            markers: {
                size: 0
            },
            yaxis: [
                {
                    title: {
                        text: 'Parcels',
                    },
                },
            ],
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


        var chart = new ApexCharts(document.querySelector("#apexincomeexpense"), options);
        chart.render();

</script>

