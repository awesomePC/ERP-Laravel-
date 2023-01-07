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
                <a class="navbar-brand" href="{{action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@index')}}"><i class="fab fa-wordpress"></i> {{__('woocommerce::lang.woocommerce')}}</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li @if(request()->segment(1) == 'woocommerce' && request()->segment(2) == 'view-sync-log') class="active" @endif><a href="{{action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@viewSyncLog')}}">@lang('woocommerce::lang.sync_log')</a></li>

                    @if (auth()->user()->can('woocommerce.access_woocommerce_api_settings'))
                        <li @if(request()->segment(1) == 'woocommerce' && request()->segment(2) == 'api-settings') class="active" @endif><a href="{{action('\Modules\Woocommerce\Http\Controllers\WoocommerceController@apiSettings')}}">@lang('woocommerce::lang.api_settings')</a></li>
                    @endif
                </ul>

            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</section>