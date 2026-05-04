</div>
    <script src="{{static_asset('backend')}}/vendor/jquery/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="{{static_asset('backend')}}/vendor/bootstrap-five/bootstrap.min.js"></script>
    <script src="{{static_asset('backend')}}/vendor/bootstrap/js/bootstrap.bundle.js"></script>
    <script src="{{static_asset('backend')}}/vendor/slimscroll/jquery.slimscroll.js"></script>
    <script src="{{static_asset('backend')}}/libs/js/main-js.js"></script>
    <script src="{{static_asset('backend')}}/vendor/charts/sparkline/jquery.sparkline.js"></script>
    <script src="{{static_asset('backend')}}/vendor/charts/morris-bundle/raphael.min.js"></script>
    <script src="{{static_asset('backend')}}/vendor/charts/morris-bundle/morris.js"></script>
    <script src="{{static_asset('backend')}}/vendor/charts/c3charts/c3.min.js"></script>
    <script src="{{static_asset('backend')}}/vendor/charts/c3charts/d3-5.4.0.min.js"></script>
    <script src="{{static_asset('backend')}}/libs/js/datepicker.min.js"></script>
    <script src="{{static_asset('backend')}}/libs/js/custom.js"></script>
    <script src="{{static_asset('backend')}}/js/dynamic-modal.js"></script>
    <script src="{{static_asset('backend')}}/js/lang.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ static_asset('backend/vendor') }}/toastr/toastr.min.js"></script>
    <script src="{{static_asset('backend')}}/libs/js/attendance.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.3.2/firebase.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{static_asset('backend')}}/js/map/current_location.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $(".sidebar-dark").addClass('show');
            if (window.innerWidth <= 991) {
                $(".sidebar-dark").removeClass('show');
                $(".sidebar-dark").addClass('text-bg-dark');
            }
            $(window).resize(function() {
                 if(window.innerWidth >= 991 ){
                    $('.sidebar-offcanvas').addClass('show');
                    $('.sidebar-offcanvas').removeClass('text-bg-dark');
                 }else{
                    $('.sidebar-offcanvas').removeClass('show');
                    $('.sidebar-offcanvas').addClass('text-bg-dark');
                 }
            });
        });

        $(document).ready(function() {
            // Will wait for everything on the page to load.
            $(window).bind('load', function() {
                $('.overlay, body').addClass('loaded');
                setTimeout(function() {
                    $('.overlay').css({
                        'display': 'none'
                    })
                }, 2000)
            });
            // Will remove overlay after 1min for users cannnot load properly.
            setTimeout(function() {
                $('.overlay, body').addClass('loaded');
            }, 60000);
        })
    </script>
    <script>
        @if(Session::has('message'))
        var type = " {{ Session::get('alert-type','info') }}"
        switch (type) {
            case 'info':
                toastr.info(" {{ Session::get('message') }} ");
                break;
            case 'success':
                toastr.success(" {{ Session::get('message') }} ");
                break;
            case 'warning':
                toastr.warning(" {{ Session::get('message') }} ");
                break;
            case 'error':
                toastr.error(" {{ Session::get('message') }} ");
                break;
        }
        @endif
    </script>
    {!! Toastr::message() !!}
    @if(env('DEMO') && env('DEMO') !== "")
        <script type="text/javascript">
            "use strict";
            $(function(){
                $('input').attr('autocomplete', 'off');
            });
        </script>
    @endif
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var yes = "{{ __('delete.yes') }}";
        var cancel = "{{ __('delete.cancel') }}";
    </script>
    <script type="text/javascript">
        "use strict";
        $(function(){
            $('.demo-login-btn').click(function(){
                $('#email').attr('value',$(this).data('email'));
                $('#password').attr('value',$(this).data('password'));
            });
            $('input').attr('autocomplete', 'off');
        });

    </script>
@stack('scripts')

<script type="text/javascript">
    "use strict";
    $(document).ready(function() {

        const firebaseConfig = {
            apiKey: "AIzaSyAyvrSvorywESEI0vG43V-C0p-6ZQQ8Cvw",
            authDomain: "wedelivery-c9a27.firebaseapp.com",
            projectId: "wedelivery-c9a27",
            storageBucket: "wedelivery-c9a27.firebasestorage.app",
            messagingSenderId: "107407767396",
            appId: "1:107407767396:web:0fabf7420b68311f822c71"
        };

        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();
        startFCM();
        function startFCM() {
            messaging.requestPermission()
                .then(function () {
                    return messaging.getToken()
                })
                .then(function (token) {
                    $.ajax({
                        url: '{{ route("notification-store.token") }}',
                        type: 'POST',
                        data: {
                            token: token
                        },
                        dataType: 'JSON',
                        success: function (result) {
                            // console.log(result);
                        },
                        error: function (error) {
                            console.log(error);
                        },
                    });
                }).catch(function (error) {
                    console.log(error);
                });
        }
        messaging.onMessage(function(payload) {
            const title = payload.notification.title;
            const options = {
                body: payload.notification.body,
                icon: payload.notification.icon,
            };
            Swal.fire({
                imageUrl:payload.notification.image,
                title: title,
                text: payload.notification.body,
                position: 'top',
                showCancelButton: true,
                confirmButtonText: yes,
                cancelButtonText: cancel,
            }).then((result) => {
                if (result.isConfirmed){
                    console.log('ok');
                }
            })
            new Notification(title, options);
        });
    });
</script>
</body>
</html>
