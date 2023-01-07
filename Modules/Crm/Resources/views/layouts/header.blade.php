@inject('request', 'Illuminate\Http\Request')
<!-- Main Header -->
<header class="main-header no-print">
    <a href="{{action('\Modules\Crm\Http\Controllers\DashboardController@index')}}" class="logo">
        <span class="logo-lg">{{ Session::get('business.name') }}</span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            &#9776;
            <span class="sr-only">Toggle navigation</span>
        </a>

        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <button id="btnCalculator" title="@lang('lang_v1.calculator')" type="button" class="btn btn-success btn-flat pull-left m-8 hidden-xs btn-sm mt-10 popover-default" data-toggle="popover" data-trigger="click" data-content='@include("layouts.partials.calculator")' data-html="true" data-placement="bottom">
                <strong>
                    <i class="fa fa-calculator fa-lg" aria-hidden="true"></i>
                </strong>
            </button>

            <div class="m-8 pull-left mt-15 hidden-xs" style="color: #fff;">
                <strong>{{ @format_date('now') }}</strong>
            </div>

            <ul class="nav navbar-nav">
                @include('layouts.partials.header-notifications')
                <!-- User Account Menu -->
                <li class="dropdown user user-menu">
                    <!-- Menu Toggle Button -->
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <!-- The user image in the navbar-->
                        @php
                            $profile_photo = auth()->user()->media;
                        @endphp
                        @if(!empty($profile_photo))
                            <img src="{{$profile_photo->display_url}}" class="user-image" alt="User Image">
                        @endif
                        <!-- hidden-xs hides the username on small devices so only the image appears. -->
                        <span>
                            {{ Auth::User()->first_name }} {{ Auth::User()->last_name }}
                        </span>
                    </a>
                    <ul class="dropdown-menu">
                        <!-- The user image in the menu -->
                        <li class="user-header">
                            @if(!empty(Session::get('business.logo')))
                                <img src="{{ url( 'uploads/business_logos/' . Session::get('business.logo') ) }}" alt="Logo">
                                </span>
                            @endif
                            <p>
                                {{ Auth::User()->first_name }} {{ Auth::User()->last_name }}
                            </p>
                        </li>
                        <li class="user-footer">
                            <div class="pull-left">
                                <a href="{{action('\Modules\Crm\Http\Controllers\ManageProfileController@getProfile')}}" class="btn btn-default btn-flat">
                                    @lang('lang_v1.profile')
                                </a>
                            </div>
                            <div class="pull-right">
                                <a href="{{action('Auth\LoginController@logout')}}" class="btn btn-default btn-flat">
                                    @lang('lang_v1.sign_out')
                                </a>
                            </div>
                        </li>
                    </ul>
                </li>
                <!-- Control Sidebar Toggle Button -->
            </ul>
        </div>
    </nav>
</header>