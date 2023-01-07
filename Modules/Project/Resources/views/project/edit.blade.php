<div class="modal-dialog  modal-lg" role="document">
    {!! Form::open(['url' => action('\Modules\Project\Http\Controllers\ProjectController@update', $project->id), 'id' => 'project_form', 'method' => 'put']) !!}
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">
                @lang('project::lang.edit_project')
            </h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                   <div class="form-group">
                        {!! Form::label('name', __('messages.name') . ':*' )!!}
                        {!! Form::text('name', $project->name, ['class' => 'form-control', 'required' ]) !!}
                   </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('description', __('lang_v1.description') . ':') !!}
                        {!! Form::textarea('description', $project->description, ['class' => 'form-control ', 'id' => 'description']); !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                   <div class="form-group">
                        {!! Form::label('contact_id', __('role.customer') . ':' )!!}
                        {!! Form::select('contact_id', $customers, $project->contact_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;']); !!}
                   </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('status', __('sale.status') .':*') !!}
                        {!! Form::select('status', $statuses, $project->status, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('lead_id', __('project::lang.lead') .':*') !!}
                        {!! Form::select('lead_id', $users, $project->lead_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                   <div class="form-group">
                        {!! Form::label('start_date', __('business.start_date') . ':' )!!}
                        {!! Form::text('start_date', !empty($project->start_date) ? @format_date($project->start_date) : '', ['class' => 'form-control datepicker', 'readonly']); !!}
                   </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('end_date', __('project::lang.end_date') .':') !!}
                        {!! Form::text('end_date', !empty($project->end_date) ? @format_date($project->end_date) : '', ['class' => 'form-control datepicker', 'readonly']); !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('user_id', __('project::lang.members') .':*') !!}
                       {!! Form::select('user_id[]', $users, $project->members->pluck('id'), ['class' => 'form-control select2', 'multiple', 'required', 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('category_id', __('project::lang.category') .':') !!}
                       {!! Form::select('category_id[]', $categories, $project->categories->pluck('id'), ['class' => 'form-control select2', 'multiple', 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-sm ladda-button" data-style="expand-right">
                <span class="ladda-label">@lang('messages.update')</span>
            </button>
             <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">
                @lang('messages.close')
            </button>
        </div>
    </div><!-- /.modal-content -->
    {!! Form::close() !!}
</div><!-- /.modal-dialog -->