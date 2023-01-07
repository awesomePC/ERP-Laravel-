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
                <a class="navbar-brand" href="{{action('\Modules\Essentials\Http\Controllers\DashboardController@hrmDashboard')}}"><i class="fa fas fa-users"></i> {{__('essentials::lang.hrm')}}</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    @can('essentials.crud_leave_type')
                        <li @if(request()->segment(2) == 'leave-type') class="active" @endif><a href="{{action('\Modules\Essentials\Http\Controllers\EssentialsLeaveTypeController@index')}}">@lang('essentials::lang.leave_type')</a></li>
                    @endcan
                    @if(auth()->user()->can('essentials.crud_all_leave') || auth()->user()->can('essentials.crud_own_leave'))
                        <li @if(request()->segment(2) == 'leave') class="active" @endif><a href="{{action('\Modules\Essentials\Http\Controllers\EssentialsLeaveController@index')}}">@lang('essentials::lang.leave')</a></li>
                    @endif
                    @if(auth()->user()->can('essentials.crud_all_attendance') || auth()->user()->can('essentials.view_own_attendance'))
                    <li @if(request()->segment(2) == 'attendance') class="active" @endif><a href="{{action('\Modules\Essentials\Http\Controllers\AttendanceController@index')}}">@lang('essentials::lang.attendance')</a></li>
                    @endif
                    <li @if(request()->segment(2) == 'payroll') class="active" @endif><a href="{{action('\Modules\Essentials\Http\Controllers\PayrollController@index')}}">@lang('essentials::lang.payroll')</a></li>

                    <li @if(request()->segment(2) == 'holiday') class="active" @endif><a href="{{action('\Modules\Essentials\Http\Controllers\EssentialsHolidayController@index')}}">@lang('essentials::lang.holiday')</a></li>
                    @can('essentials.crud_department')
                    <li @if(request()->get('type') == 'hrm_department') class="active" @endif><a href="{{action('TaxonomyController@index') . '?type=hrm_department'}}">@lang('essentials::lang.departments')</a></li>
                    @endcan
                    
                    @can('essentials.crud_designation')
                    <li @if(request()->get('type') == 'hrm_designation') class="active" @endif><a href="{{action('TaxonomyController@index') . '?type=hrm_designation'}}">@lang('essentials::lang.designations')</a></li>
                    @endcan

                    @if(auth()->user()->can('essentials.access_sales_target'))
                        <li @if(request()->segment(1) == 'hrm' && request()->segment(2) == 'sales-target') class="active" @endif><a href="{{action('\Modules\Essentials\Http\Controllers\SalesTargetController@index')}}">@lang('essentials::lang.sales_target')</a></li>
                    @endif

                    @if(auth()->user()->can('edit_essentials_settings'))
                        <li @if(request()->segment(1) == 'hrm' && request()->segment(2) == 'settings') class="active" @endif><a href="{{action('\Modules\Essentials\Http\Controllers\EssentialsSettingsController@edit')}}">@lang('business.settings')</a></li>
                    @endif
                </ul>

            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</section>