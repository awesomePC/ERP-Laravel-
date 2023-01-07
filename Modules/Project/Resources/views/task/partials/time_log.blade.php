<div class="panel box box-info">
    <div class="box-header with-border">
        <h4 class="box-title">
            <a data-toggle="collapse" data-parent="#accordion" href="#project_task_time_log" class="collapsed" aria-expanded="false">
                @lang('project::lang.time_logs')
            </a>
        </h4>
        @if($can_crud_timelog || $is_lead_or_admin)
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-sm btn-primary add_time_log pull-right" data-href="{{action('\Modules\Project\Http\Controllers\ProjectTimeLogController@create', ['task_id' => $project_task->id, 'project_id' => $project_task->project_id])}}">
                    @lang('messages.add')&nbsp;
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        @endif
    </div>
    <div id="project_task_time_log" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
        <div class="box-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th> @lang('project::lang.start_date_time')</th>
                        <th>@lang('project::lang.end_date_time')</th>
                        <th>@lang('project::lang.work_hour')</th>
                        <th>@lang('role.user')</th>
                        <th>@lang('brand.note')</th>
                    </tr>
                </thead>
                <tbody id="task-timelog">
                    @includeIf('project::task.partials.time_log_table_body')
                </tbody>
            </table>
        </div>
    </div>
</div>