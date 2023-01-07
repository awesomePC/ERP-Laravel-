@if($__is_essentials_enabled)
<li class="bg-navy treeview {{ in_array($request->segment(1), ['essentials']) ? 'active active-sub' : '' }}">
    <a href="#">
        <i class="fas fa-check-circle"></i>
        <span class="title">@lang('essentials::lang.essentials')</span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>

    <ul class="treeview-menu">
        <li class="{{ $request->segment(2) == 'todo' ? 'active active-sub' : '' }}">
            <a href="{{action('\Modules\Essentials\Http\Controllers\ToDoController@index')}}">
                <i class="fa fa-list-ul"></i>
                <span class="title">@lang('essentials::lang.todo')</span>
            </a>
        </li>
		<li class="{{ ($request->segment(2) == 'document' && $request->get('type') != 'memos') ? 'active active-sub' : '' }}">
				<a href="{{action('\Modules\Essentials\Http\Controllers\DocumentController@index')}}">
				<i class="fa fa-file"></i>
				<span class="title"> @lang('essentials::lang.document') </span>
			</a>
		</li>
        <li class="{{ ($request->segment(2) == 'document' && $request->get('type') == 'memos') ? 'active active-sub' : '' }}">
            <a href="{{action('\Modules\Essentials\Http\Controllers\DocumentController@index') .'?type=memos'}}">
                <i class="fa fa-envelope-open"></i>
                <span class="title">
                    @lang('essentials::lang.memos')
                </span>
            </a>
        </li>
        <li class="{{ $request->segment(2) == 'reminder' ? 'active active-sub' : ''}}">
            <a href="{{action('\Modules\Essentials\Http\Controllers\ReminderController@index')}}">
                <i class="fa fa-bell"></i>
                <span class="title">
                    @lang('essentials::lang.reminders')
                </span>
            </a>
        </li>
        @if(auth()->user()->can('essentials.view_message') || auth()->user()->can('essentials.create_message'))
        <li class="{{ $request->segment(2) == 'messages' ? 'active active-sub' : ''}}">
            <a href="{{action('\Modules\Essentials\Http\Controllers\EssentialsMessageController@index')}}">
                <i class="fa fa-comments-o"></i>
                <span class="title">
                    @lang('essentials::lang.messages')
                </span>
            </a>
        </li>
        @endif
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