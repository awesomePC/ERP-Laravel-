@extends('layouts.app')
@section('title', __('project::lang.view_project'))
@section('content')
<section class="content-header">
    <h1>
        <i class="fas fa-check-circle"></i>
        {{ucFirst($project->name)}}
    </h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-md-12">
           <!-- Custom Tabs -->
           <!-- project_id to be used in datatable -->
           	<input type="hidden" name="project_id" id="project_id" value="{{$project->id}}">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="
                        @if($tab_view == 'overview')
                            active
                        @else
                            ''
                        @endif">
                        <a href="#project_overview" data-toggle="tab" aria-expanded="true" data-url="{{action('\Modules\Project\Http\Controllers\ProjectController@show', ['id' => $project->id]).'?view=overview'}}">
                        	<i class="fas fa-tachometer-alt"></i>
                        	@lang('project::lang.overview')
                        </a>
                    </li>

                    <li class="
                        @if($tab_view == 'activities')
                            active
                        @else
                            ''
                        @endif">
                        <a href="#activities" data-toggle="tab" aria-expanded="true">
                            <i class="fas fa-chart-line"></i>
                            @lang('lang_v1.activities')
                        </a>
                    </li>

                    <li class="
                        @if($tab_view == 'project_task')
                            active
                        @else
                            ''
                        @endif">
                        <a href="#project_task" data-toggle="tab" aria-expanded="true">
                        	<i class="fa fa-tasks"></i>
                        	@lang('project::lang.task')
                        </a>
                    </li>

                    @if(isset($project->settings['enable_timelog']) && $project->settings['enable_timelog'])
                        <li class="
                            @if($tab_view == 'time_log')
                                active
                            @else
                                ''
                            @endif">
                        	<a href="#time_log" data-toggle="tab" aria-expanded="true">
                        		<i class="fas fa-clock"></i>
                        		@lang('project::lang.time_logs')
                        	</a>
                        </li>
                    @endif

                    @if(isset($project->settings['enable_notes_documents']) && $project->settings['enable_notes_documents'])
                        <li class="
                            @if($tab_view == 'documents_and_notes')
                                active
                            @else
                                ''
                            @endif">
                            <a href="#documents_and_notes" data-toggle="tab" aria-expanded="true">
                                <i class="fas fa-file-image"></i>
                                @lang('project::lang.documents_and_notes')
                            </a>
                        </li>
                    @endif
                    
                    @if((isset($project->settings['enable_invoice']) && $project->settings['enable_invoice']) && $is_lead_or_admin)
                    <li class="
                        @if($tab_view == 'project_invoices')
                            active
                        @else
                            ''
                        @endif">
                        <a href="#project_invoices" data-toggle="tab" aria-expanded="true">
                            <i class="fa fa-file"></i>
                            @lang('project::lang.invoices')
                        </a>
                    </li>
                    @endif

                    @if($is_lead_or_admin)
                    <li class="
                        @if($tab_view == 'project_settings')
                            active
                        @else
                            ''
                        @endif">
                        <a href="#project_settings" data-toggle="tab" aria-expanded="true">
                            <i class="fa fa-cogs"></i>
                            @lang('role.settings')
                        </a>
                    </li>
                    @endif
                </ul>

                <div class="tab-content">
                    <div class="tab-pane
                        @if($tab_view == 'overview')
                            active
                        @else
                            ''
                        @endif" id="project_overview"> 
                        @includeIf('project::project.partials.overview')
                    </div>

                    <div class="tab-pane
                        @if($tab_view == 'activities')
                            active
                        @else
                            ''
                        @endif" id="activities">
                        <ul class="timeline">
                        </ul>
                    </div>

                    <div class="tab-pane
                        @if($tab_view == 'project_task')
                            active
                        @else
                            ''
                        @endif" id="project_task">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('assigned_to_filter', __('project::lang.assigned_to') . ':') !!}
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-user"></i>
                                        </span>
                                        {!! Form::select('assigned_to_filter', $project_members, null, ['class' => 'form-control select2', 'placeholder' => __('messages.all'), 'style' => 'width: 100%;']); !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 status_filter
                                @if(isset($project->settings['task_view']) &&
                                $project->settings['task_view'] == 'kanban')
                                    hide
                                @endif">
                                <div class="form-group">
                                    {!! Form::label('status_filter', __('sale.status') . ':') !!}
                                    {!! Form::select('status_filter', $statuses, null, ['class' => 'form-control select2', 'placeholder' => __('messages.all'), 'style' => 'width: 100%;']); !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('priority_filter', __('project::lang.priority') .':') !!}
                                    {!! Form::select('priority_filter', $priorities, null, ['class' => 'form-control select2', 'placeholder' => __('messages.all'), 'style' => 'width: 100%;']); !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('due_date_filter', __('project::lang.due_date') . ':') !!}
                                    {!! Form::select('due_date_filter', $due_dates, null, ['class' => 'form-control select2', 'placeholder' => __('messages.all'), 'style' => 'width: 100%;']); !!}
                                </div>
                            </div>
                        </div>
                        @includeIf('project::task.index')
                    </div>

                    @if(isset($project->settings['enable_timelog']) && $project->settings['enable_timelog'])
                        <div class="tab-pane
                            @if($tab_view == 'time_log')
                                active
                            @else
                                ''
                            @endif" id="time_log">
                        	@includeIf('project::time_logs.index')
                        </div>
                    @endif

                    @if(isset($project->settings['enable_notes_documents']) && $project->settings['enable_notes_documents'])
                        <!-- model id like project_id, user_id -->
                        <input type="hidden" name="notable_id" id="notable_id" value="{{$project->id}}">
                        <!-- model name like App\User -->
                        <input type="hidden" name="notable_type" id="notable_type" value="Modules\Project\Entities\Project">
                        <div class="tab-pane document_note_body
                            @if($tab_view == 'documents_and_notes')
                                active
                            @else
                                ''
                            @endif" id="documents_and_notes">
                        </div>
                    @endif

                    @if((isset($project->settings['enable_invoice']) && $project->settings['enable_invoice']) && $is_lead_or_admin)
                        <div class="tab-pane
                            @if($tab_view == 'project_invoices')
                                active
                            @else
                                ''
                            @endif" id="project_invoices">
                            @includeif('project::invoice.index')
                        </div>
                    @endif
                    @if($is_lead_or_admin)
                        <div class="tab-pane
                            @if($tab_view == 'project_settings')
                                active
                            @else
                                ''
                            @endif" id="project_settings">
                            @includeIf('project::settings.create')
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade project_task_model" tabindex="-1" role="dialog"></div>
    <div class="modal fade" tabindex="-1" role="dialog" id="time_log_model"></div>
    <div class="modal fade view_project_task_model" tabindex="-1" role="dialog"></div>
    <div class="modal fade payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel"></div>
    <div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel"></div>
</section>
<link rel="stylesheet" href="{{ asset('modules/project/sass/project.css?v=' . $asset_v) }}">
@endsection
@section('javascript')
<script src="{{ asset('modules/project/js/project.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
<script type="text/javascript">
    var tab_view = '{!!$tab_view!!}';

    if (tab_view == 'activities') {
        initializeActivities();
    } else if (tab_view == 'project_task') {
        initializeProjectTaskDatatable();
    } else if (tab_view == 'time_log') {
        initializeTimeLogDatatable();
    } else if (tab_view == 'documents_and_notes') {
        initializeNotesDataTable();
    } else if (tab_view == 'project_invoices') {
        initializeInvoiceDatatable();
    }
</script>
@endsection