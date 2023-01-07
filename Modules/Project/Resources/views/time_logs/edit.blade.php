<div class="modal-dialog" role="document">
    {!! Form::open(['url' => action('\Modules\Project\Http\Controllers\ProjectTimeLogController@update', $project_task_time_log->id), 'id' => 'time_log_form', 'method' => 'put']) !!}
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">
                @lang('project::lang.edit_time_log')
            </h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('project_task_id', __('project::lang.task') .':') !!}
                        {!! Form::select('project_task_id', $project_tasks, $project_task_time_log->project_task_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;']); !!}
                    </div>
                    {!! Form::hidden('project_id', $project_task_time_log->project_id, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('start_datetime', __('project::lang.start_date_time') . ':*' )!!}
                        {!! Form::text('start_datetime', @format_datetime($project_task_time_log->start_datetime), ['class' => 'form-control datetimepicker', 'readonly', 'required']); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('end_datetime', __('project::lang.end_date_time') .':*') !!}
                        {!! Form::text('end_datetime', @format_datetime($project_task_time_log->end_datetime), ['class' => 'form-control datetimepicker', 'readonly', 'required']); !!}
                    </div>
                </div>
                @if($is_lead_or_admin)
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('user_id', __('role.user') .':*') !!}
                            {!! Form::select('user_id', $project_members, $project_task_time_log->user_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('note', __('brand.note') . ':') !!}
                        {!! Form::textarea('note', $project_task_time_log->note, ['class' => 'form-control ', 'id' => 'note', 'rows' => '4']); !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-sm">
                @lang('messages.update')
            </button>
             <button type="button" class="btn btn-dafualt btn-sm" data-dismiss="modal">
                @lang('messages.close')
            </button>
        </div>
    </div>
    {!! Form::close() !!}
</div>