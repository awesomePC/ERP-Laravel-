@inject('request', 'Illuminate\Http\Request')

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) ? 'rtl' : 'ltr'}}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title') - {{ Session::get('business.name') }}</title> 

        <script src="{{ asset('AdminLTE/plugins/pace/pace.min.js?v=' . $asset_v) }}"></script>
        <link rel="stylesheet" href="{{ asset('AdminLTE/plugins/pace/pace.css?v='.$asset_v) }}">

        @include('layouts.partials.css')

        @yield('css')
    </head>

    <body class="hold-transition">
        <div class="wrapper">
            <script type="text/javascript">
                if(localStorage.getItem("upos_sidebar_collapse") == 'true'){
                    var body = document.getElementsByTagName("body")[0];
                    body.className += " sidebar-collapse";
                }
            </script>
        
            <!-- Content Wrapper. Contains page content -->
            <div class="container-fluid">
                @yield('content')
                <!-- This will be printed -->
                <section class="invoice print_section" id="receipt_section">
                </section>
                
            </div>

        </div>

        @include('layouts.partials.javascripts')
    </body>

</html>