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
                <a class="navbar-brand" href="{{action('\Modules\Repair\Http\Controllers\DashboardController@index')}}"><i class="fas fa-wrench"></i> {{__('repair::lang.repair')}}</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    @if(auth()->user()->can('job_sheet.create') || auth()->user()->can('job_sheet.view_assigned') || auth()->user()->can('job_sheet.view_all'))
                        <li @if(request()->segment(2) == 'job-sheet' && empty(request()->segment(3))) class="active" @endif>
                            <a href="{{action('\Modules\Repair\Http\Controllers\JobSheetController@index')}}">
                                @lang('repair::lang.job_sheets')
                            </a>
                        </li>
                    @endif

                    @can('job_sheet.create')
                        <li @if(request()->segment(2) == 'job-sheet' && request()->segment(3) == 'create') class="active" @endif>
                            <a href="{{action('\Modules\Repair\Http\Controllers\JobSheetController@create')}}">
                                @lang('repair::lang.add_job_sheet')
                            </a>
                        </li>
                    @endcan

                    @if(auth()->user()->can('repair.view') || auth()->user()->can('repair.view_own'))
                        <li @if(request()->segment(2) == 'repair' && empty(request()->segment(3))) class="active" @endif><a href="{{action('\Modules\Repair\Http\Controllers\RepairController@index')}}">@lang('repair::lang.list_invoices')</a></li>
                    @endif

                    @can('repair.create')
                        <li @if(request()->segment(2) == 'repair' && request()->segment(3) == 'create') class="active" @endif><a href="{{ action('SellPosController@create'). '?sub_type=repair'}}">@lang('repair::lang.add_invoice')</a></li>
                    @endcan

                    @if(auth()->user()->can('brand.view') || auth()->user()->can('brand.create'))
                        <li @if(request()->segment(1) == 'brands') class="active" @endif><a href="{{action('BrandController@index')}}">@lang('brand.brands')</a></li>
                    @endif

                    @if (auth()->user()->can('edit_repair_settings'))
                        <li @if(request()->segment(1) == 'repair' && request()->segment(2) == 'repair-settings') class="active" @endif><a href="{{action('\Modules\Repair\Http\Controllers\RepairSettingsController@index')}}">@lang('messages.settings')</a></li>
                    @endif
                </ul>

            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</section>