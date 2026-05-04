<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta name="keywords" content="{{ settings()->name}}" />
    <meta name="description" content="{{ settings()->name }} - delivery">
    <meta name="author" content="{{ settings()->name }}">
    <title>{{ settings()->name }} | Home</title>
    <link rel="icon" href="{{ settings()->favicon_image}}" />
    <link rel="stylesheet" href="{{ static_asset('vue-frontend/frontend/assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ static_asset('vue-frontend/frontend/assets/css/all.min.css') }}" />
    <link rel="stylesheet" href="{{ static_asset('vue-frontend/frontend/assets/vendor/aos/aos.css') }}">
    <link rel="stylesheet" href="{{ static_asset('vue-frontend/frontend/assets/css/style.css') }}" />
    <link rel="stylesheet" href="{{ static_asset('vue-frontend/frontend/assets/vendor/venobox/venobox.min.css') }}" />
    <link rel="stylesheet" href="{{ static_asset('vue-frontend/frontend/assets/vendor/jarallax/jarallax.min.css') }}">
    <link rel="stylesheet" href="{{ static_asset('vue-frontend/frontend/assets/vendor/odometer/odometer.css') }}" />
    <link rel="stylesheet" href="{{ static_asset('vue-frontend/frontend/assets/vendor/flag-icons/css/flag-icons.min.css') }}" />
    <link rel="stylesheet" href="{{ static_asset('vue-frontend/frontend/css/timeline.css') }}" />
    @vite('resources/sass/app.scss')
    <style>
        :root {
            /* --bs-primary:{{ settings('theme_color') }};
            --bg-primary:{{ settings('theme_color') }};
            --bs-deep-primary:{{ settings('theme_hover_color') }};
            --text-color:{{ settings('text_color') }};
            --bs-outline-primary:{{ settings('theme_color') }}; */
        }

        #nprogress .bar {
            background: var(--bs-primary) !important;
            height: 4px;
            z-index: 9999;
        }
    </style>
</head>

<body>
    <div id="app"></div>

    <!-- scripts -->
    <script src="{{ static_asset('vue-frontend/frontend/assets/js/jquery.min.js') }}"></script>
    <script src="{{ static_asset('vue-frontend/frontend/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ static_asset('vue-frontend/frontend/assets/js/plyr.js') }}"></script>
    <script src="{{ static_asset('vue-frontend/frontend/assets/vendor/aos/aos.js') }}"></script>
    <script src="{{ static_asset('vue-frontend/frontend/assets/vendor/venobox/venobox.min.js') }}"></script>
    <script src="{{ static_asset('vue-frontend/frontend/assets/vendor/jarallax/jarallax.min.js') }}"></script>
    <script src="{{ static_asset('vue-frontend/frontend/assets/vendor/odometer/odometer.min.js') }}"></script>
    <script src="{{ static_asset('vue-frontend/frontend/assets/js/theme.js') }}"></script>
    <script>
        @if(@auth()->user()->shipper)
        window.Laravel = {
            authUserData: {
                !!auth()->user() !!
            },
            authUser: {
                !!auth()->user()->shipper!!
            },
            SITE_TITLE: "{{ settings()->name }}",
            GOOGLE_MAPS_API_KEY: "{{ env('GOOGLE_MAPS_API_KEY') }}",
        }
        @else
        window.Laravel = {
            authUserData: '',
            authUser: '',
            SITE_TITLE: "{{ settings()->name }}",
            GOOGLE_MAPS_API_KEY: "{{ env('GOOGLE_MAPS_API_KEY') }}"
        }
        @endif
    </script>
    @vite('resources/js/app.js')
    {!! Toastr::message() !!}
</body>

</html>