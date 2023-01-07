<!doctype html>
<html lang="{{ config('app.locale') }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title')</title>

        <!-- Fonts -->
        <!-- <link href="https://fonts.googleapis.com/css?family=Raleway:100,300,600" rel="stylesheet" type="text/css"> -->
        
        <link rel="stylesheet" href="{{ asset('css/vendor.css') }}">

        <!-- Styles -->
        <style>
            body {
                min-height: 100vh;
                background-color: #fff;
                color: #000;
            }
            .navbar-default {
                background-color: transparent;
                border: none;
            }
            .navbar-static-top {
                margin-bottom: 19px;
            }
            .navbar-default .navbar-nav>li>a {
                color: #000;
                font-weight: 600;
                font-size: 15px
            }
            .navbar-default .navbar-nav>li>a:hover{
                color: #000;
            }
            .navbar-default .navbar-brand {
                color: #000;
            }
        </style>
    </head>

    <body>
        @include('layouts.partials.home_header')
        <div class="container">
            <div class="content">
                @yield('content')
            </div>
        </div>
        @include('layouts.partials.javascripts')

    <!-- Scripts -->
    <script src="{{ asset('js/login.js?v=' . $asset_v) }}"></script>
    @yield('javascript')
    </body>
</html>