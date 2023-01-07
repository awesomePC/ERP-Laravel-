<div class="modal-dialog" role="document">
    {!! Form::open(['url' => action('\Modules\Project\Http\Controllers\TaskController@postTaskStatus', $project_task->id), 'id' => 'change_status', 'method' => 'put']) !!}
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">
                @lang("project::lang.change_status")
            </h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('status', __('sale.status') .':*') !!}
                        {!! Form::select('status', $statuses, $project_task->status, ['class' => 'form-control select2', 'required', 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
            </div>
            {!! Form::hidden('project_id', $project_task->project_id, ['class' => 'form-control']) !!}
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-sm">
                @lang('messages.update')
            </button>
             <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                @lang('messages.close')
            </button>
        </div>
    </div><!-- /.modal-content -->
     {!! Form::close() !!}
</div><!-- /.modal-dialog -->