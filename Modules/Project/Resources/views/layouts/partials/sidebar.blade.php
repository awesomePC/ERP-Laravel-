<li class="bg-maroon treeview {{ in_array($request->segment(1), ['project']) ? 'active active-sub' : '' }}">
	<a href="#">
        <i class="fa fa-check-square-o"></i>
        <span class="title">
        	@lang('project::lang.project')
        </span>
        <span class="pull-right-container">
            <i class="fa fa-angle-left pull-right"></i>
        </span>
    </a>
    <ul class="treeview-menu">
    	<li class="{{ $request->segment(2) == 'project' ? 'active active-sub' : '' }}">
            <a href="{{action('\Modules\Project\Http\Controllers\ProjectController@index')}}">
                <i class="fas fa-check-circle"></i>
                <span class="title">@lang('project::lang.projects')</span>
            </a>
        </li>
        <li class="{{ $request->segment(2) == 'project-task' ? 'active active-sub' : '' }}">
            <a href="{{action('\Modules\Project\Http\Controllers\TaskController@index')}}">
                <i class="fa fa-tasks"></i>
                <span class="title">@lang('project::lang.tasks')</span>
            </a>
        </li>
    </ul>
</li>