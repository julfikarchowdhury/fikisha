@extends('frontend.layouts.master')

@section('title')
    {{ __('levels.parcel_tracking') }} | {{ @settings()->name }}
@endsection

@section('content')
<section class="container-fluid pb-5">
    <div class="container pt-5 pb-4">
        <div class="row">
            <div class="col-lg-10 m-auto">
                <h3 class="font-size-1-5rem display-6 font-weight-bold text-center mb-3">
                    {{ __('levels.parcel_tracking') }}:
                    <span class="text-primary"># {{ $trackingToken }}</span>
                </h3>
                <div class="card mb-3 shadow-sm border-0">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                            <div>
                                <span class="text-muted mr-2">Live Status</span>
                                <span id="track-status-badge" class="badge badge-info px-3 py-2">-</span>
                            </div>
                            <button type="button" id="traffic-toggle-btn" class="btn btn-sm btn-outline-primary">Traffic: ON</button>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2"><strong>Pickup:</strong> <span id="track-pickup">-</span></div>
                            <div class="col-md-6 mb-2"><strong>Drop:</strong> <span id="track-drop">-</span></div>
                            <div class="col-md-4 mb-2"><strong>ETA:</strong> <span id="track-eta">-</span></div>
                            <div class="col-md-4 mb-2"><strong>Remaining Distance:</strong> <span id="track-distance">-</span></div>
                            <div class="col-md-4 mb-2"><strong>Last Update:</strong> <span id="track-updated">-</span></div>
                        </div>
                    </div>
                </div>
                <div class="map-wrap">
                    <div id="tracking-map"></div>
                    <div class="map-legend">
                        <span class="dot dot-rider"></span> Rider
                        <span class="dot dot-pickup ml-3"></span> Pickup
                        <span class="dot dot-drop ml-3"></span> Drop
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .map-wrap {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(14, 40, 80, 0.1);
        border: 1px solid #e8edf4;
    }

    #tracking-map {
        height: 460px;
        width: 100%;
        background: #f7f9fc;
    }

    .map-legend {
        position: absolute;
        left: 12px;
        bottom: 12px;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 999px;
        padding: 6px 12px;
        font-size: 12px;
        color: #1f2d3d;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
        z-index: 2;
    }

    .dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        margin-right: 4px;
        vertical-align: middle;
    }

    .dot-rider { background: #1565c0; }
    .dot-pickup { background: #2e7d32; }
    .dot-drop { background: #c62828; }
</style>
@endpush

@section('scripts')
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places"></script>
    <script>
        const trackingToken = @json($trackingToken);
        const trackingApiUrl = @json(url('/api/tracking/' . $trackingToken));
        const pusherKey = @json($pusherKey);
        const pusherCluster = @json($pusherCluster);
        const STATUS_MARKETPLACE_PICKED_UP = @json(\App\Enums\ParcelStatus::MARKETPLACE_PICKED_UP);
        const STATUS_MARKETPLACE_DELIVERED = @json(\App\Enums\ParcelStatus::MARKETPLACE_DELIVERED);

        let map;
        let riderMarker;
        let pickupMarker;
        let dropMarker;
        let pickupInfoWindow;
        let dropInfoWindow;
        let riderInfoWindow;
        let lastRiderPosition = null;
        let latestData = {};
        let directionsService;
        let directionsRenderer;
        let trafficLayer;
        let fallbackPolyline;
        let trafficEnabled = true;
        let selectedRouteIndex = 0;
        let lastUserMapInteractionAt = 0;
        let lastRouteOrigin = null;
        let lastRouteSignature = null;
        let lastRouteRequestedAt = 0;
        let routeRequestInFlight = false;

        const MIN_ROUTE_RECALC_METERS = 100;
        const ROUTE_RECALC_INTERVAL_MS = 25000;
        const USER_INTERACTION_LOCK_MS = 15000;
        const MAX_AUTO_ZOOM = 16;
        const SINGLE_POINT_ZOOM = 14;

        function toLatLng(lat, lng) {
            return new google.maps.LatLng(lat, lng);
        }

        function haversineMeters(a, b) {
            if (!a || !b) {
                return 0;
            }
            const toRad = (deg) => (deg * Math.PI) / 180;
            const earthRadius = 6371000;
            const dLat = toRad(b.lat - a.lat);
            const dLng = toRad(b.lng - a.lng);
            const lat1 = toRad(a.lat);
            const lat2 = toRad(b.lat);

            const x = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.sin(dLng / 2) * Math.sin(dLng / 2) * Math.cos(lat1) * Math.cos(lat2);

            return 2 * earthRadius * Math.atan2(Math.sqrt(x), Math.sqrt(1 - x));
        }

        function shouldRecalculateRoute(origin, routeSignature) {
            const now = Date.now();
            if (!lastRouteOrigin) {
                return true;
            }
            if (lastRouteSignature !== routeSignature) {
                return true;
            }
            if ((now - lastRouteRequestedAt) >= ROUTE_RECALC_INTERVAL_MS) {
                return true;
            }
            return haversineMeters(lastRouteOrigin, origin) >= MIN_ROUTE_RECALC_METERS;
        }

        function normalizeStatus(status) {
            const normalized = Number(status);
            return Number.isFinite(normalized) ? normalized : null;
        }

        function isPickedUpOrLater(status) {
            const normalized = normalizeStatus(status);
            return normalized === STATUS_MARKETPLACE_PICKED_UP || normalized === STATUS_MARKETPLACE_DELIVERED;
        }

        function updateInfo(data) {
            const statusText = (typeof data.status_name === 'string' && data.status_name.length > 0)
                ? data.status_name
                : (data.status !== undefined && data.status !== null ? String(data.status) : '-');

            const statusBadge = document.getElementById('track-status-badge');
            if (statusBadge) {
                statusBadge.textContent = statusText;
                statusBadge.classList.remove('badge-info', 'badge-warning', 'badge-success', 'badge-secondary');
                const statusNum = normalizeStatus(data.status);
                if (statusNum === STATUS_MARKETPLACE_DELIVERED) {
                    statusBadge.classList.add('badge-success');
                } else if (statusNum === STATUS_MARKETPLACE_PICKED_UP) {
                    statusBadge.classList.add('badge-info');
                } else if (statusNum === null) {
                    statusBadge.classList.add('badge-secondary');
                } else {
                    statusBadge.classList.add('badge-warning');
                }
            }
            if (data.pickup_address !== undefined) {
                document.getElementById('track-pickup').textContent = data.pickup_address || '-';
            }
            if (data.drop_address !== undefined) {
                document.getElementById('track-drop').textContent = data.drop_address || '-';
            }
            if (data.updated_at !== undefined) {
                document.getElementById('track-updated').textContent = data.updated_at || '-';
            }
        }

        function updateRouteInfo(leg) {
            const eta = leg && leg.duration && leg.duration.text ? leg.duration.text : '-';
            const distance = leg && leg.distance && leg.distance.text ? leg.distance.text : '-';
            document.getElementById('track-eta').textContent = eta;
            document.getElementById('track-distance').textContent = distance;
        }

        function canAutoPanMap() {
            if (!lastUserMapInteractionAt) {
                return true;
            }
            return (Date.now() - lastUserMapInteractionAt) > USER_INTERACTION_LOCK_MS;
        }

        function updateTrafficButtonLabel() {
            const btn = document.getElementById('traffic-toggle-btn');
            if (!btn) {
                return;
            }
            btn.textContent = 'Traffic: ' + (trafficEnabled ? 'ON' : 'OFF');
            btn.classList.toggle('btn-outline-primary', trafficEnabled);
            btn.classList.toggle('btn-outline-secondary', !trafficEnabled);
        }

        function applyTrafficLayerState() {
            if (!trafficLayer) {
                return;
            }
            trafficLayer.setMap(trafficEnabled ? map : null);
            updateTrafficButtonLabel();
        }

        function initMap(centerLat, centerLng) {
            map = new google.maps.Map(document.getElementById('tracking-map'), {
                zoom: 13,
                center: toLatLng(centerLat, centerLng),
                mapTypeControl: false,
                fullscreenControl: true,
                streetViewControl: false,
                gestureHandling: 'greedy',
            });
            trafficLayer = new google.maps.TrafficLayer();
            applyTrafficLayerState();
            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer({
                suppressMarkers: true,
                preserveViewport: false,
                polylineOptions: {
                    strokeColor: '#0d47a1',
                    strokeOpacity: 0.95,
                    strokeWeight: 7,
                },
                routeIndex: 0,
            });
            directionsRenderer.setMap(map);
            fallbackPolyline = new google.maps.Polyline({
                map: map,
                path: [],
                geodesic: true,
                strokeColor: '#0d47a1',
                strokeOpacity: 0.85,
                strokeWeight: 5,
            });

            map.addListener('dragstart', function () {
                lastUserMapInteractionAt = Date.now();
            });
            map.addListener('zoom_changed', function () {
                lastUserMapInteractionAt = Date.now();
            });
            map.addListener('click', function () {
                lastUserMapInteractionAt = Date.now();
            });

            directionsRenderer.addListener('directions_changed', function () {
                const directions = directionsRenderer.getDirections();
                if (!directions || !directions.routes || !directions.routes.length) {
                    return;
                }

                selectedRouteIndex = directionsRenderer.getRouteIndex() || 0;
                const activeRoute = directions.routes[selectedRouteIndex] || directions.routes[0];
                const activeLeg = activeRoute && activeRoute.legs && activeRoute.legs[0] ? activeRoute.legs[0] : null;
                updateRouteInfo(activeLeg);
            });
        }

        function fitBoundsIfNeeded() {
            if (!map) {
                return;
            }
            const bounds = new google.maps.LatLngBounds();
            let points = 0;
            [pickupMarker, dropMarker, riderMarker].forEach((marker) => {
                if (marker && marker.getPosition()) {
                    bounds.extend(marker.getPosition());
                    points += 1;
                }
            });
            if (points >= 2) {
                map.fitBounds(bounds);
                google.maps.event.addListenerOnce(map, 'idle', function () {
                    if (map.getZoom() > MAX_AUTO_ZOOM) {
                        map.setZoom(MAX_AUTO_ZOOM);
                    }
                });
            } else if (points === 1) {
                let center = null;
                if (riderMarker && riderMarker.getPosition()) {
                    center = riderMarker.getPosition();
                } else if (pickupMarker && pickupMarker.getPosition()) {
                    center = pickupMarker.getPosition();
                } else if (dropMarker && dropMarker.getPosition()) {
                    center = dropMarker.getPosition();
                }
                if (center) {
                    map.setCenter(center);
                    map.setZoom(SINGLE_POINT_ZOOM);
                }
            }
        }

        function fitRouteBounds(route) {
            if (!map || !route || !route.bounds || !canAutoPanMap()) {
                return;
            }
            map.fitBounds(route.bounds, 60);
            google.maps.event.addListenerOnce(map, 'idle', function () {
                if (map.getZoom() > MAX_AUTO_ZOOM) {
                    map.setZoom(MAX_AUTO_ZOOM);
                }
            });
        }

        function markerIcon(fillColor) {
            return {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 9,
                fillColor: fillColor,
                fillOpacity: 1,
                strokeColor: '#ffffff',
                strokeWeight: 2,
            };
        }

        function markerLabel(text) {
            return {
                text: text,
                color: '#ffffff',
                fontWeight: '700',
                fontSize: '11px',
            };
        }

        function clearFallbackPolyline() {
            if (fallbackPolyline) {
                fallbackPolyline.setPath([]);
            }
        }

        function drawFallbackLine(origin, destination) {
            if (!fallbackPolyline || !origin || !destination) {
                return;
            }
            fallbackPolyline.setPath([
                toLatLng(origin.lat, origin.lng),
                toLatLng(destination.lat, destination.lng),
            ]);
            fitBoundsIfNeeded();
        }

        function drawRoute(origin, destination, options = {}) {
            if (!origin || !destination || !directionsService || !directionsRenderer) {
                return;
            }
            const force = !!options.force;
            const waypoints = Array.isArray(options.waypoints) ? options.waypoints : [];
            const routeSignature = [
                destination.lat,
                destination.lng,
                ...waypoints.map((wp) => [wp.location.lat, wp.location.lng].join(','))
            ].join('|');
            if (routeRequestInFlight) {
                return;
            }
            if (!force && !shouldRecalculateRoute(origin, routeSignature)) {
                return;
            }

            routeRequestInFlight = true;
            lastRouteRequestedAt = Date.now();
            lastRouteOrigin = { lat: origin.lat, lng: origin.lng };
            lastRouteSignature = routeSignature;

            directionsService.route({
                origin: origin,
                destination: destination,
                travelMode: google.maps.TravelMode.DRIVING,
                provideRouteAlternatives: true,
                waypoints: waypoints,
                optimizeWaypoints: false,
            }, function (result, status) {
                routeRequestInFlight = false;
                if (status === 'OK' && result && result.routes && result.routes[0]) {
                    clearFallbackPolyline();
                    directionsRenderer.setDirections(result);
                    const safeRouteIndex = Math.min(selectedRouteIndex, result.routes.length - 1);
                    directionsRenderer.setRouteIndex(safeRouteIndex);
                    directionsRenderer.setOptions({
                        polylineOptions: {
                            strokeColor: '#0d47a1',
                            strokeOpacity: 0.95,
                            strokeWeight: 7,
                        },
                    });
                    const leg = result.routes[safeRouteIndex] && result.routes[safeRouteIndex].legs && result.routes[safeRouteIndex].legs[0]
                        ? result.routes[safeRouteIndex].legs[0]
                        : (result.routes[0].legs && result.routes[0].legs[0] ? result.routes[0].legs[0] : null);
                    updateRouteInfo(leg);
                    fitRouteBounds(result.routes[safeRouteIndex] || result.routes[0]);
                } else {
                    drawFallbackLine(origin, destination);
                }
            });
        }

        function ensureMarkers(data) {
            latestData = Object.assign({}, latestData, data || {});
            const currentStatus = normalizeStatus(latestData.status);
            const pickedUpStage = isPickedUpOrLater(currentStatus);

            if (!map) {
                const centerLat = latestData.rider_lat ?? latestData.pickup_lat ?? latestData.drop_lat ?? 0;
                const centerLng = latestData.rider_lng ?? latestData.pickup_lng ?? latestData.drop_lng ?? 0;
                initMap(centerLat, centerLng);
            }

            if (latestData.pickup_lat !== null && latestData.pickup_lat !== undefined &&
                latestData.pickup_lng !== null && latestData.pickup_lng !== undefined && !pickupMarker) {
                pickupMarker = new google.maps.Marker({
                    position: toLatLng(latestData.pickup_lat, latestData.pickup_lng),
                    map: map,
                    label: markerLabel('P'),
                    icon: markerIcon('#2e7d32'),
                    title: 'Pickup',
                });
                pickupInfoWindow = new google.maps.InfoWindow({ content: '<strong>Pickup</strong>' });
                pickupMarker.addListener('click', function () {
                    pickupInfoWindow.open(map, pickupMarker);
                });
            }
            if (pickupMarker) {
                pickupMarker.setIcon(markerIcon(pickedUpStage ? '#43a047' : '#2e7d32'));
                pickupMarker.setLabel(markerLabel('P'));
            }

            if (latestData.drop_lat !== null && latestData.drop_lat !== undefined &&
                latestData.drop_lng !== null && latestData.drop_lng !== undefined && !dropMarker) {
                dropMarker = new google.maps.Marker({
                    position: toLatLng(latestData.drop_lat, latestData.drop_lng),
                    map: map,
                    label: markerLabel('D'),
                    icon: markerIcon('#c62828'),
                    title: 'Drop',
                });
                dropInfoWindow = new google.maps.InfoWindow({ content: '<strong>Drop</strong>' });
                dropMarker.addListener('click', function () {
                    dropInfoWindow.open(map, dropMarker);
                });
            }

            if (latestData.rider_lat !== null && latestData.rider_lat !== undefined &&
                latestData.rider_lng !== null && latestData.rider_lng !== undefined) {
                const next = { lat: latestData.rider_lat, lng: latestData.rider_lng };
                if (!riderMarker) {
                    riderMarker = new google.maps.Marker({
                        position: toLatLng(next.lat, next.lng),
                        map: map,
                        icon: markerIcon('#1565c0'),
                        label: markerLabel('R'),
                        title: 'Rider',
                    });
                    riderInfoWindow = new google.maps.InfoWindow({ content: '<strong>Rider</strong>' });
                    riderMarker.addListener('click', function () {
                        riderInfoWindow.open(map, riderMarker);
                    });
                    lastRiderPosition = next;
                } else {
                    animateMarker(lastRiderPosition, next, 1000);
                    lastRiderPosition = next;
                }
                if (canAutoPanMap()) {
                    map.panTo(toLatLng(next.lat, next.lng));
                }
            }

            const dropPoint = (latestData.drop_lat !== null && latestData.drop_lat !== undefined &&
                latestData.drop_lng !== null && latestData.drop_lng !== undefined)
                ? { lat: latestData.drop_lat, lng: latestData.drop_lng }
                : null;
            const pickupPoint = (latestData.pickup_lat !== null && latestData.pickup_lat !== undefined &&
                latestData.pickup_lng !== null && latestData.pickup_lng !== undefined)
                ? { lat: latestData.pickup_lat, lng: latestData.pickup_lng }
                : null;

            if (dropPoint || pickupPoint) {
                const routeOrigin = (latestData.rider_lat !== null && latestData.rider_lat !== undefined &&
                    latestData.rider_lng !== null && latestData.rider_lng !== undefined)
                    ? { lat: latestData.rider_lat, lng: latestData.rider_lng }
                    : pickupPoint;

                const waypoints = [];
                let routeDestination = null;

                if (pickedUpStage) {
                    // After pickup: only show rider -> receiver route.
                    routeDestination = dropPoint || pickupPoint;
                } else if (pickupPoint && dropPoint) {
                    // After accept and before pickup: show rider -> sender -> receiver.
                    routeDestination = dropPoint;
                    const originIsPickup = routeOrigin &&
                        Math.abs(routeOrigin.lat - pickupPoint.lat) < 0.000001 &&
                        Math.abs(routeOrigin.lng - pickupPoint.lng) < 0.000001;
                    if (!originIsPickup) {
                        waypoints.push({ location: pickupPoint, stopover: true });
                    }
                } else {
                    routeDestination = pickupPoint || dropPoint;
                }

                drawRoute(routeOrigin, routeDestination, { force: false, waypoints: waypoints });
            }
        }

        function animateMarker(from, to, duration) {
            if (!from || !to || !riderMarker) {
                return;
            }

            const start = performance.now();
            const latDelta = to.lat - from.lat;
            const lngDelta = to.lng - from.lng;

            function step(timestamp) {
                const progress = Math.min((timestamp - start) / duration, 1);
                const currentLat = from.lat + (latDelta * progress);
                const currentLng = from.lng + (lngDelta * progress);
                riderMarker.setPosition(toLatLng(currentLat, currentLng));
                if (progress < 1) {
                    requestAnimationFrame(step);
                }
            }

            requestAnimationFrame(step);
        }

        async function loadInitialData() {
            const response = await fetch(trackingApiUrl);
            const payload = await response.json();
            if (!payload || !payload.data) {
                return;
            }
            updateInfo(payload.data);
            ensureMarkers(payload.data);

            const initialOrigin = (payload.data.rider_lat !== null && payload.data.rider_lat !== undefined &&
                payload.data.rider_lng !== null && payload.data.rider_lng !== undefined)
                ? { lat: payload.data.rider_lat, lng: payload.data.rider_lng }
                : ((payload.data.pickup_lat !== null && payload.data.pickup_lat !== undefined &&
                    payload.data.pickup_lng !== null && payload.data.pickup_lng !== undefined)
                    ? { lat: payload.data.pickup_lat, lng: payload.data.pickup_lng }
                    : null);

            const initialPickup = (payload.data.pickup_lat !== null && payload.data.pickup_lat !== undefined &&
                payload.data.pickup_lng !== null && payload.data.pickup_lng !== undefined)
                ? { lat: payload.data.pickup_lat, lng: payload.data.pickup_lng }
                : null;

            const initialDrop = (payload.data.drop_lat !== null && payload.data.drop_lat !== undefined &&
                payload.data.drop_lng !== null && payload.data.drop_lng !== undefined)
                ? { lat: payload.data.drop_lat, lng: payload.data.drop_lng }
                : null;

            const initialPickedUpStage = isPickedUpOrLater(payload.data.status);
            const initialWaypoints = [];
            let initialDestination = null;

            if (initialPickedUpStage) {
                initialDestination = initialDrop || initialPickup;
            } else if (initialPickup && initialDrop) {
                initialDestination = initialDrop;
                const initialOriginIsPickup = initialOrigin &&
                    Math.abs(initialOrigin.lat - initialPickup.lat) < 0.000001 &&
                    Math.abs(initialOrigin.lng - initialPickup.lng) < 0.000001;
                if (!initialOriginIsPickup) {
                    initialWaypoints.push({ location: initialPickup, stopover: true });
                }
            } else {
                initialDestination = initialPickup || initialDrop;
            }

            drawRoute(initialOrigin, initialDestination, { force: true, waypoints: initialWaypoints });
        }

        function subscribeToUpdates() {
            if (!pusherKey || !pusherCluster) {
                return;
            }

            const pusher = new Pusher(pusherKey, {
                cluster: pusherCluster,
                forceTLS: true,
            });

            const channel = pusher.subscribe('tracking.' + trackingToken);
            channel.bind('rider.location.updated', function (data) {
                updateInfo({
                    status: data.status,
                    updated_at: data.updated_at,
                });
                ensureMarkers({
                    status: data.status,
                    rider_lat: data.lat,
                    rider_lng: data.lng,
                });
            });
        }

        function startPollingFallback() {
            window.setInterval(function () {
                loadInitialData();
            }, 30000);
        }

        function bindTrafficToggle() {
            const btn = document.getElementById('traffic-toggle-btn');
            if (!btn) {
                return;
            }
            btn.addEventListener('click', function () {
                trafficEnabled = !trafficEnabled;
                applyTrafficLayerState();
            });
            updateTrafficButtonLabel();
        }

        document.addEventListener('DOMContentLoaded', function () {
            bindTrafficToggle();
            loadInitialData();
            subscribeToUpdates();
            startPollingFallback();
        });
    </script>
@endsection

