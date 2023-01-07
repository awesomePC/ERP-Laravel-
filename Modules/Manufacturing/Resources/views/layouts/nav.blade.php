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
                <a class="navbar-brand" href="{{action('\Modules\Manufacturing\Http\Controllers\RecipeController@index')}}"><i class="fas fa-industry"></i> {{__('manufacturing::lang.manufacturing')}}</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    @can('manufacturing.access_recipe')
                        <li @if(request()->segment(1) == 'manufacturing' && in_array(request()->segment(2), ['recipe', 'add-ingredient'])) class="active" @endif><a href="{{action('\Modules\Manufacturing\Http\Controllers\RecipeController@index')}}">@lang('manufacturing::lang.recipe')</a></li>
                    @endcan

                    @can('manufacturing.access_production')
                        <li @if(request()->segment(2) == 'production') class="active" @endif><a href="{{action('\Modules\Manufacturing\Http\Controllers\ProductionController@index')}}">@lang('manufacturing::lang.production')</a></li>

                        <li @if(request()->segment(1) == 'manufacturing' && request()->segment(2) == 'settings') class="active" @endif><a href="{{action('\Modules\Manufacturing\Http\Controllers\SettingsController@index')}}">@lang('messages.settings')</a></li>

                        <li @if(request()->segment(2) == 'report') class="active" @endif><a href="{{action('\Modules\Manufacturing\Http\Controllers\ProductionController@getManufacturingReport')}}">@lang('manufacturing::lang.manufacturing_report')</a></li>
                    @endcan
                </ul>

            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</section>