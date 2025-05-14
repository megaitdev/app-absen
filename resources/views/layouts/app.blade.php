<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Library CSS --}}
        @foreach ($csses as $css)
            <link rel="stylesheet" href="{{ asset($css) }}">
        @endforeach
        <link rel="stylesheet" href="{{ asset('library/bootstrap/dist/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('library/izitoast/dist/css/iziToast.min.css') }}">
        <link rel="stylesheet" href="{{ asset('library/fontawesome/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('css/components.css') }}">
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>App Absen | {{ $title }}</title>
        <link rel="icon" href="{{ asset('img/logo/mega-it-dark.png') }}">

        <!-- Fonts -->
        <link rel="stylesheet" href="{{ asset('css/font.css') }}">

        <!-- Scripts -->
    </head>

    <body class="sidebar-mini">
        <div id="app">
            <div class="main-wrapper">
                <!-- Header -->
                @include('components.header')

                <!-- Sidebar -->
                @include('components.sidebar')

                <!-- Content -->
                @yield('content')

                <!-- Footer -->
                @include('components.footer')
            </div>
        </div>

        <!-- General JS Scripts -->
        <script src="{{ asset('library/jquery/dist/jquery.min.js') }}"></script>
        <script src="{{ asset('library/popper.js/dist/umd/popper.js') }}"></script>
        <script src="{{ asset('library/tooltip.js/dist/umd/tooltip.js') }}"></script>
        <script src="{{ asset('library/jquery-nicescroll/dist/jquery.nicescroll.min.js') }}"></script>
        <script src="{{ asset('library/moment/min/moment.min.js') }}"></script>
        <script src="{{ asset('library/moment-timezone/moment-timezone.js') }}"></script>
        <script src="{{ asset('library/moment/min/locales.min.js') }}"></script>
        <script src="{{ asset('library/izitoast/dist/js/iziToast.min.js') }}"></script>
        <script src="{{ asset('library/bootstrap/dist/js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/stisla.js') }}"></script>
        <script src="{{ asset('js/base.js') }}"></script>

        <!-- Template JS File -->
        <script src="{{ asset('js/scripts.js') }}"></script>
        @foreach ($scripts as $script)
            <script src="{{ asset($script) }}"></script>
        @endforeach
    </body>

</html>
