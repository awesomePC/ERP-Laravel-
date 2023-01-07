@if($can_crud_timelog || $is_lead_or_admin)
    <button type="button" class="btn btn-sm btn-primary time_log_btn pull-right" data-href="{{action('\Modules\Project\Http\Controllers\ProjectTimeLogController@create', ['project_id' => $project->id])}}">
        @lang('messages.add')&nbsp;
        <i class="fa fa-plus"></i>
    </button> <br><br>
@endif
<div class="table-responsive">
    <table class="table table-bordered table-striped" id="time_logs_table">
        <thead>
            <tr>
                <th> @lang('messages.action')</th>
                <th> @lang('project::lang.task')</th>
                <th> @lang('project::lang.start_date_time')</th>
                <th>@lang('project::lang.end_date_time')</th>
                <th>@lang('project::lang.work_hour')</th>
                <th>@lang('role.user')</th>
                <th>@lang('brand.note')</th>
            </tr>
        </thead>
    </table>
</div>