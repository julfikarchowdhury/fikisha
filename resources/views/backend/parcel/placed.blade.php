@extends('backend.partials.master')
@section('title')
    {{ __('levels.order_placed') }}
@endsection
@section('maincontent')
<style>
    .custom-map {
        width: 100%;
        height: 40rem;
        border: 1px solid #696cff;
        border-radius: 5px;
    }
</style>
    <!-- wrapper  -->
    <div class="container-fluid  dashboard-content">
        <!-- pageheader -->
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
                                    <a href="{{ route('parcel.index') }}" class="breadcrumb-link"> {{ __('parcel.title') }}</a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="" class="breadcrumb-link active">{{ __('levels.order_placed') }}</a>
                                </li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <!-- end pageheader -->
        <div class="row">
            <!--data table  -->
            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <p class="h4">{{ __('levels.order_placed') }} </p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="h4">{{ __('parcel.parcel') }} {{ __('levels.status') }}</p>
                                </div>
                                <div>
                                    <a href="{{ route('parcel.logs', $parcel->id) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        {{ __('parcel.tracking') }} <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                @php
                                    $unassigned = \App\Models\Backend\ParcelEvent::where(['parcel_id' => $parcel->id])
                                        ->whereIn('parcel_status', [
                                            App\Enums\ParcelStatus::PENDING,
                                            App\Enums\ParcelStatus::DELIVERY_MAN_ASSIGN_CANCEL,
                                            App\Enums\ParcelStatus::DELIVERY_RE_SCHEDULE_CANCEL,
                                        ])->orderByDesc('id')
                                        ->first();
                                        
                                    $driverAssign = \App\Models\Backend\ParcelEvent::where(['parcel_id' => $parcel->id])
                                        ->whereIn('parcel_status', [
                                            App\Enums\ParcelStatus::DELIVERY_MAN_ASSIGN,
                                            App\Enums\ParcelStatus::DELIVERY_RE_SCHEDULE,
                                            App\Enums\ParcelStatus::CONFIRMED,
                                            App\Enums\ParcelStatus::CONFIRMED_BOOKING,
                                            App\Enums\ParcelStatus::UNCONFIRMED,
                                            App\Enums\ParcelStatus::UNCONFIRMED_BOOKING,
                                        ])->orderByDesc('id')
                                        ->first();
            
                                    $processing = \App\Models\Backend\ParcelEvent::where(['parcel_id' => $parcel->id])
                                        ->where('parcel_status', \App\Enums\ParcelStatus::PICKED_UP)
                                        ->orderByDesc('id')
                                        ->first();
            
                                    $onTheWay = \App\Models\Backend\ParcelEvent::where(['parcel_id' => $parcel->id])
                                        ->whereIn('parcel_status', [\App\Enums\ParcelStatus::HEADING_TO_DROP_OFF_HUB, \App\Enums\ParcelStatus::HEADING_TO_DELIVERY_POINT, \App\Enums\ParcelStatus::DROPPED_OFF_AT_HUB])
                                        ->orderByDesc('id')
                                        ->first();
            
                                    $delivered = \App\Models\Backend\ParcelEvent::where(['parcel_id' => $parcel->id])
                                        ->whereIn('parcel_status', [\App\Enums\ParcelStatus::DELIVERED, \App\Enums\ParcelStatus::PARTIAL_DELIVERED])
                                        ->orderByDesc('id')
                                        ->first(); 
                                    $failure = \App\Models\Backend\ParcelEvent::where(['parcel_id' => $parcel->id])
                                        ->whereIn('parcel_status', [
                                            App\Enums\ParcelStatus::DELIVERY_FAILURE,
                                            App\Enums\ParcelStatus::DELIVERY_FAILED,
                                            App\Enums\ParcelStatus::PARCEL_CANCEL,
                                            App\Enums\ParcelStatus::RETURNED_MERCHANT,
                                        ])
                                        ->orderByDesc('id')
                                        ->first(); 
                                @endphp
                                <div class="table-responsive">
                                    <div class="border-box dott-details logs">
                                        <div class="border-dotted"></div>
                                        <div class="d-flex ">
                                            <span class="icon-span mt-1"><i class="fa-solid  fa-circle-check me-2 fs-5 text-success"></i></span>
                                            <div>
                                                <h3 class="text-dark mb-0">Unassigned</h3>
                                                <span>{!! @dateFormat($unassigned->created_at) !!} {!! @date('h:i a', strtotime($unassigned->created_at)) !!}</span>
                                            </div>
                                        </div>
                                        <br />
                                        <div class="mt-2 d-flex ">
                                            <span class="icon-span mt-1">
                                                <i class="@if ($driverAssign) fa-solid fa-circle-check text-success @else  fa-regular  fa-circle text-primary @endif  me-2 fs-5"></i>
                                            </span>
                                            <div class="">
                                                <h3 class="text-dark mb-0">Assigned</h3>
                                                @if ($driverAssign)
                                                    <span>{!! @dateFormat($driverAssign->created_at) !!} {!! @date('h:i a', strtotime($driverAssign->created_at)) !!}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <br />
                                        <div class="mt-2 d-flex ">
                                            <span class="icon-span mt-1">
                                                <i class="@if ($driverAssign && $processing) fa-solid fa-circle-check text-success @else  fa-regular  fa-circle text-primary @if (!$driverAssign) unactive @endif @endif   me-2 fs-5 "></i>
                                            </span>
                                            <div class="@if ($driverAssign && $processing) @else  @if (!$driverAssign) unactive @endif  @endif">
                                                <h3 class="text-dark mb-0">Processing</h3>
                                                @if ($processing)
                                                    <span>{!! @dateFormat($processing->created_at) !!} {!! @date('h:i a', strtotime($processing->created_at)) !!}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <br />
                                      
                                        @if ($failure)
                                            <div class="mt-2 d-flex ">
                                                <span class="icon-span mt-1">
                                                    <i class="@if ( $parcel->status == App\Enums\ParcelStatus::DELIVERED || $parcel->status == App\Enums\ParcelStatus::PARTIAL_DELIVERED) fa-solid fa-circle-check text-success @else fa-regular fa-circle @if (!$processing) unactive @endif  @endif  me-2 fs-5 text-primary"></i>
                                                </span>
                                                <div class="@if (!$processing) unactive @endif">
                                                    <h3 class="text-dark mb-0">Failure</h3>
                                                    @if ($failure)
                                                        <span>{!! @dateFormat($failure->created_at) !!} {!! @date('h:i a', strtotime($failure->created_at)) !!}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @else
                                            <div class="mt-2 d-flex ">
                                                <span class="icon-span mt-1">
                                                    <i class="@if ( $parcel->status == App\Enums\ParcelStatus::DELIVERED || $parcel->status == App\Enums\ParcelStatus::PARTIAL_DELIVERED) fa-solid fa-circle-check text-success @else fa-regular fa-circle @if (!$processing) unactive @endif  @endif  me-2 fs-5 text-primary"></i>
                                                </span>
                                                <div class="@if (!$processing) unactive @endif">
                                                    <h3 class="text-dark mb-0">Delivered</h3>
                                                    @if ($delivered)
                                                        <span>{!! @dateFormat($delivered->created_at) !!} {!! @date('h:i a', strtotime($delivered->created_at)) !!}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div> 
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <p class="h4">{{ __('levels.route') }}</p>
                                <p>
                                    <i class="fa-solid fa-route me-2 fs-4 text-dark"></i>{{ __('parcel.distance') }} : <b class="text-dark">{{ $parcel->distance_km }} km</b>
                                </p>
                            </div>
                            <div class="card-body">
                                <div class="border-box dott-details">
                                    <div class="border-dotted"></div>
                                    <div class="d-flex">
                                        <span class="icon-span"><i class="fa-regular fa-circle me-2 fs-5 text-primary"></i></span>
                                        <div>
                                            {{ $parcel->pickup_address }}<br />
                                            <b class="text-dark" id="autocomplete-input">{{ $parcel->pickup_location }}</b>
                                        </div>
                                    </div><br />
                                    <div class="mt-2 d-flex">
                                        <span class="icon-span"><i class="fa fa-location-dot me-2 fs-4 text-primary"></i></span>
                                        <div>
                                            {{ $parcel->customer_address }}<br />
                                            <b class="text-dark" id="autocomplete">{{ $parcel->drop_location }}</b>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>
                    <div class="col-md-8 mb-3">
                        <section class="mt-1">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="ls-inner-container fixed-map">
                                        <div id="fixed-map-container">
                                            <div id="mapDirection" class="custom-map" data-map-zoom="9" data-map-scroll="true"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
            <!--end data table-->
        </div>
    </div>
    <!-- nd wrapper  -->
@endsection()

<!-- css  -->
@push('styles')
    <link rel="stylesheet" href="{{static_asset('backend')}}/css/logs.css">
    <link rel="stylesheet" href="{{static_asset('backend/css/map/style.css')}}">
@endpush
@push('scripts')
    <script>
        var urlImage = '{{ static_asset('backend/images/default/motorcycle.png') }}';
        var parcels = @json($mapParcels);
        var mapLat = '';
        var mapLong = '';
    </script>
    <script type="text/javascript" src="{{ static_asset('backend/js/parcel/map/map.js') }}"></script>
    <script type="text/javascript" src="{{ static_asset('backend/js/parcel/map/typed.js') }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initAutocomplete"></script>
    <script type="text/javascript" src="{{static_asset('backend/js/parcel/map/infobox.min.js')}}"></script>
    <script type="text/javascript" src="{{static_asset('backend/js/parcel/map/markerclusterer.js')}}"></script>
    <script type="text/javascript" src="{{static_asset('backend/js/parcel/map/placed_direction_show.js')}}"></script>
    <script type="text/javascript">
    //Start Google Map Directions
        googleMapDirections();
        function googleMapDirections() {
            var pointA = new google.maps.LatLng(23.7956037, 90.3536548);
            var myOptions = {
                zoom: 15,
                center: pointA,
            };
            var map = new google.maps.Map(
                document.getElementById("mapDirection"),
                myOptions
            );

            // Instantiate a directions service.
            var directionsService = new google.maps.DirectionsService();
            var directionsDisplay = new google.maps.DirectionsRenderer({
                map: map,
            });
            // get route from A to B
            var originAddress = '{{ $parcel->pickup_location }}';
            console.log(originAddress);
            var destinationAddress = '{{ $parcel->drop_location }}';
            if (originAddress && destinationAddress) {
                directionsService.route(
                    {
                        origin: originAddress,
                        destination: destinationAddress,
                        avoidTolls: true,
                        avoidHighways: false,
                        provideRouteAlternatives: false,
                        travelMode: google.maps.TravelMode.DRIVING,
                        unitSystem: google.maps.UnitSystem.METRIC,
                    },
                    function (response, status) {
                        if (status == google.maps.DirectionsStatus.OK) {
                            directionsDisplay.setDirections(response);
                        } else {
                            window.alert("Directions request failed due to " + status);
                        }
                    }
                );
            }
        }
        //End Google Map Directions
    </script>
@endpush
