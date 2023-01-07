<section class="no-print">
    <nav class="navbar navbar-default bg-white m-4">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{action('\Modules\Superadmin\Http\Controllers\SuperadminController@index')}}"><i class="fa fas fa-users-cog"></i> {{__('superadmin::lang.superadmin')}}</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li @if(request()->segment(1) == 'superadmin' && request()->segment(2) == 'business') class="active" @endif><a href="{{action('\Modules\Superadmin\Http\Controllers\BusinessController@index')}}">@lang('superadmin::lang.all_business')</a></li>

                    <li @if(request()->segment(1) == 'superadmin' && request()->segment(2) == 'superadmin-subscription') class="active" @endif><a href="{{action('\Modules\Superadmin\Http\Controllers\SuperadminSubscriptionsController@index')}}">@lang('superadmin::lang.subscription')</a></li>

                    <li @if(request()->segment(1) == 'superadmin' && request()->segment(2) == 'packages') class="active" @endif><a href="{{action('\Modules\Superadmin\Http\Controllers\PackagesController@index')}}">@lang('superadmin::lang.subscription_packages')</a></li>

                    <li @if(request()->segment(1) == 'superadmin' && request()->segment(2) == 'settings') class="active" @endif><a href="{{action('\Modules\Superadmin\Http\Controllers\SuperadminSettingsController@edit')}}">@lang('superadmin::lang.super_admin_settings')</a></li>

                    <li @if(request()->segment(1) == 'superadmin' && request()->segment(2) == 'communicator') class="active" @endif><a href="{{action('\Modules\Superadmin\Http\Controllers\CommunicatorController@index')}}">@lang('superadmin::lang.communicator')</a></li>
                </ul>

            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</section>