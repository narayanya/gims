<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon.png') }}">

    <!-- jsvectormap css -->
    <link href="{{ asset('assets/libs/jsvectormap/css/jsvectormap.min.css') }}" rel="stylesheet" type="text/css" />

    <!--Swiper slider css-->
    <link href="{{ asset('assets/libs/swiper/swiper-bundle.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Layout config Js -->
    <script src="{{ asset('assets/js/layout.js') }}"></script>
    <!-- Bootstrap Css -->
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="{{ asset('assets/css/custom.min.css') }}" rel="stylesheet" type="text/css" />

</head>
<body @auth class="vertical-collpsed" @endauth @guest class="login-bg" @endguest>
    @guest
@if (Route::has('login'))
<main class="py-4">
    @yield('content')
</main>
     
@endif

@else
<!-- Begin page  -->
    <div id="layout-wrapper">
        @include('layouts.header')
        @include('layouts.sidebarmenu')

        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <main class="py-4">
                        @yield('content')
                    </main>
                </div>
                <!-- container-fluid -->
                
            </div>
            <!-- End Page-content -->
            
            @include('layouts.footer')
            
        </div>
        <!-- end main content-->
        
    </div>
    <!-- END layout-wrapper -->
@endguest
     <!--start back-to-top-->
    <button onclick="topFunction()" class="btn btn-danger btn-icon" id="back-to-top">
        <i class="ri-arrow-up-line"></i>
    </button>
    <!--end back-to-top-->

    <!-- JAVASCRIPT -->
    <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
    <script src="{{ asset('assets/libs/feather-icons/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/pages/plugins/lord-icon-2.1.0.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="{{ asset('assets/js/plugins.js') }}"></script>

    <!-- App js -->
    <script src="{{ asset('assets/js/app.js') }}"></script>
     @yield('scripts')
    @yield('modals')
    @stack('scripts')
    @auth
    @php
        $pageVisitLogId = Session::get('page_visit_log_id');
        $pageExitUrl    = route('logs.page-exit');
        $csrfToken      = csrf_token();
    @endphp
    <script>
    (function () {
        var logId = @if($pageVisitLogId) {{ (int) $pageVisitLogId }} @else null @endif;
        if (!logId) return;
        var url   = '{{ $pageExitUrl }}';
        var token = '{{ $csrfToken }}';
        function sendExit() {
            var data = JSON.stringify({ log_id: logId, _token: token });
            if (navigator.sendBeacon) {
                navigator.sendBeacon(url, new Blob([data], { type: 'application/json' }));
            } else {
                fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token }, body: data, keepalive: true });
            }
        }
        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'hidden') sendExit();
        });
        window.addEventListener('pagehide', sendExit);
    })();
    </script>
    @endauth
</body>
</html>
