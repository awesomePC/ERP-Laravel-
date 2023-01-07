@if($can_crud_task || $is_lead_or_admin)
<button type="button" class="btn btn-sm btn-primary task_btn pull-right m-5" data-href="{{action('\Modules\Project\Http\Controllers\TaskController@create', ['project_id' => $project->id])}}">
    @lang('messages.add')&nbsp;
    <i class="fa fa-plus"></i>
</button>
@endif
<div class="btn-group btn-group-toggle pull-right m-5" data-toggle="buttons">
    <label class="btn btn-info btn-sm 
        @if((!empty($project->settings) && !isset($project->settings['task_view'])) || (isset($project->settings['task_view']) &&
                $project->settings['task_view'] == 'list_view'))
            active
        @endif">
        <input type="radio" name="task_view" value="list_view" class="task_view" 
           @if((!empty($project->settings) && !isset($project->settings['task_view'])) || (isset($project->settings['task_view']) &&
                $project->settings['task_view'] == 'list_view'))
                checked
            @endif>
        @lang('project::lang.list_view')
    </label>
    <label class="btn btn-info btn-sm
        @if(isset($project->settings['task_view']) &&
        $project->settings['task_view'] == 'kanban')
            active
        @endif">
        <input type="radio" name="task_view" value="kanban" class="task_view" 
            @if(isset($project->settings['task_view']) &&
            $project->settings['task_view'] == 'kanban')
                checked
            @endif>
        @lang('project::lang.kanban_board')
    </label>
</div>
<br><br>
<div class="table-responsive
    @if(isset($project->settings['task_view']) &&
        $project->settings['task_view'] != 'list_view')
        hide
    @endif">
    <table class="table table-bordered table-striped" id="project_task_table">
        <thead>
            <tr>
                <th> @lang('messages.action')</th>
                <th class="col-md-4"> @lang('project::lang.subject')</th>
                <th class="col-md-2"> @lang('project::lang.assigned_to')</th>
                <th> @lang('project::lang.priority')</th>
                <th> @lang('business.start_date')</th>
                <th>@lang('project::lang.due_date')</th>
                <th>@lang('sale.status')</th>
                <th>@lang('project::lang.assigned_by')</th>
                <th>@lang('project::lang.task_custom_field_1')</th>
                <th>@lang('project::lang.task_custom_field_2')</th>
                <th>@lang('project::lang.task_custom_field_3')</th>
                <th>@lang('project::lang.task_custom_field_4')</th>
            </tr>
        </thead>
    </table>
</div>

<div class="custom-kanban-board
    @if(isset($project->settings['task_view']) &&
    $project->settings['task_view'] != 'kanban')
        hide
    @endif">
    <div class="page">
        <div class="main">
            <div class="meta-tasks-wrapper">
                <div id="myKanban" class="meta-tasks"></div>
            </div>
        </div>
    </div>
</div>