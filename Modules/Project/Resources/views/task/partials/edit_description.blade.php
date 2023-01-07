<div class="task_description toggle_description_fields">
    {!! $project_task->description !!}
</div>
<!-- form open -->
{!! Form::open(['url' => action('\Modules\Project\Http\Controllers\TaskController@postTaskDescription', ['id' => $project_task->id, 'project_id' => $project_task->project_id]), 'id' => 'update_task_description', 'method' => 'put']) !!}
    <div class="form-group">
        {!! Form::textarea('description', $project_task->description, ['class' => 'form-control ', 'id' => 'edit_description_of_task']); !!}
    </div>
    <button type="submit" class="btn btn-primary btn-sm ladda-button save-description-btn" data-style="expand-right">
        <span class="ladda-label">@lang('messages.update')</span>
    </button>
     <button type="button" class="btn btn-default btn-sm close_update_task_description_form">
        @lang('messages.close')
    </button>
{!! Form::close() !!}
<!-- /form close -->