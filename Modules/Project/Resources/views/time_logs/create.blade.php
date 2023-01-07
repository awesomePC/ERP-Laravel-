<div class="modal-dialog" role="document">
    {!! Form::open(['action' => '\Modules\Project\Http\Controllers\ProjectTimeLogController@store', 'id' => 'time_log_form', 'method' => 'post']) !!}
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">
                @lang('project::lang.add_time_log')
            </h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <!-- added from(task/timelog) -->
                {!! Form::hidden('added_from', $added_from, ['class' => 'form-control']) !!}
                {!! Form::hidden('project_id', $project_id, ['class' => 'form-control']) !!}
                @if($added_from == 'task')
                    {!! Form::hidden('project_task_id', $task_id, ['class' => 'form-control']) !!}
                @else
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('project_task_id', __('project::lang.task') .':') !!}
                            {!! Form::select('project_task_id', $project_tasks, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                @endif
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('start_datetime', __('project::lang.start_date_time') . ':*' )!!}
                        {!! Form::text('start_datetime', '', ['class' => 'form-control datetimepicker', 'readonly', 'required']); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('end_datetime', __('project::lang.end_date_time') .':*') !!}
                        {!! Form::text('end_datetime', '', ['class' => 'form-control datetimepicker', 'readonly', 'required']); !!}
                    </div>
                </div>
                @if($is_lead_or_admin)
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('user_id', __('role.user') .':*') !!}
                            {!! Form::select('user_id', $project_members, null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('note', __('brand.note') . ':') !!}
                        {!! Form::textarea('note', null, ['class' => 'form-control ', 'id' => 'note', 'rows' => '4']); !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-sm">
                @lang('messages.save')
            </button>
             <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                @lang('messages.close')
            </button>
        </div>
    </div>
    {!! Form::close() !!}
</div>