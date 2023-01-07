@if($__is_essentials_enabled)
<li class="bg-info treeview {{ in_array($request->segment(1), ['hrm']) ? 'active active-sub' : '' }}">
    <a href="#">
        <i class="fa fa-users"></i>
        <span class="title">@lang('essentials::lang.hrm')</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>

    <ul class="treeview-menu">
        @can('add_essentials_leave_type')
            <li class="{{ $request->segment(2) == 'leave-type' ? 'active active-sub' : '' }}">
                <a href="{{action('\Modules\Essentials\Http\Controllers\EssentialsLeaveTypeController@index')}}">
                    <i class="fa fa-star"></i>
                    <span class="title">@lang('essentials::lang.leave_type')</span>
                </a>
            </li>
        @endcan
        <li class="{{ $request->segment(2) == 'leave' ? 'active active-sub' : '' }}">
            <a href="{{action('\Modules\Essentials\Http\Controllers\EssentialsLeaveController@index')}}">
                <i class="fa fa-user-times"></i>
                <span class="title">@lang('essentials::lang.leave')</span>
            </a>
        </li>

        <li class="{{ $request->segment(2) == 'attendance' ? 'active active-sub' : '' }}">
            <a href="{{action('\Modules\Essentials\Http\Controllers\AttendanceController@index')}}">
                <i class="fa fa-check-square-o"></i>
                <span class="title">@lang('essentials::lang.attendance')</span>
            </a>
        </li>
        @can('essentials.add_allowance_and_deduction')
        <li class="{{ $request->segment(2) == 'allowance-deduction' ? 'active active-sub' : '' }}">
            <a href="{{action('\Modules\Essentials\Http\Controllers\EssentialsAllowanceAndDeductionController@index')}}">
                <i class="fa fa-money"></i>
                <span class="title">@lang('essentials::lang.allowance_and_deduction')</span>
            </a>
        </li>
        @endcan
        <li class="{{ $request->segment(2) == 'payroll' ? 'active active-sub' : '' }}">
            <a href="{{action('\Modules\Essentials\Http\Controllers\PayrollController@index')}}">
                <i class="fa fa-money"></i>
                <span class="title">@lang('essentials::lang.payroll')</span>
            </a>
        </li>
        <li class="{{ $request->segment(2) == 'holiday' ? 'active active-sub' : '' }}">
            <a href="{{action('\Modules\Essentials\Http\Controllers\EssentialsHolidayController@index')}}">
                <i class="fa fa-suitcase"></i>
                <span class="title">@lang('essentials::lang.holiday')</span>
            </a>
        </li>
        @can('edit_essentials_settings')
            <li class="{{ $request->segment(2) == 'settings' ? 'active active-sub' : '' }}">
                <a href="{{action('\Modules\Essentials\Http\Controllers\EssentialsSettingsController@edit')}}">
                    <i class="fa fa-cogs"></i>
                    <span class="title">@lang('business.settings')</span>
                </a>
            </li>
        @endcan
    </ul>
</li>
@endif