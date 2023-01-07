@extends('layouts.app')
@section('title', __('project::lang.my_tasks'))
@section('content')
@include('project::layouts.nav')
<section class="content-header">
	<h3>
		<i class="fa fa-tasks"></i>
		@lang('project::lang.tasks')
	</h3>
</section>
<section class="content">
	@component('components.filters', ['title' => __('report.filters')])
		<div class="row">
			<div class="col-md-3">
			    <div class="form-group">
			        {!! Form::label('project_id', __('project::lang.project') . ':') !!}
			        {!! Form::select('project_id', $projects, null, ['class' => 'form-control select2', 'placeholder' => __('messages.all'), 'style' => 'width: 100%;']); !!}
			    </div>
			</div>
			@if($is_admin)
				<div class="col-md-3">
				    <div class="form-group">
				        {!! Form::label('assigned_to_filter', __('project::lang.assigned_to') . ':') !!}
				        <div class="input-group">
				            <span class="input-group-addon">
				                <i class="fa fa-user"></i>
				            </span>
				            {!! Form::select('assigned_to_filter', $users, null, ['class' => 'form-control select2', 'placeholder' => __('messages.all'), 'style' => 'width: 100%;']); !!}
				        </div>
				    </div>
				</div>
			@endif
			<div class="col-md-3 status_filter">
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
	@endcomponent
	@php
		$tool = '<div class="btn-group btn-group-toggle pull-right m-5" data-toggle="buttons">
    			<label class="btn btn-info btn-sm active">
        			<input type="radio" name="task_view" value="list_view" class="my_task_view" checked>'
        			. __("project::lang.list_view").'
    			</label>
				<label class="btn btn-info btn-sm">
				    <input type="radio" name="task_view" value="kanban" class="my_task_view">
				    '. __("project::lang.kanban_board").'
				</label>
			</div>';
	@endphp
	@component('components.widget', ['class' => 'box-primary', 'title' => __( 'project::lang.my_tasks'), 'tool' => $tool])
		<div class="table-responsive">
		    <table class="table table-bordered table-striped" id="my_task_table">
		        <thead>
		            <tr>
		            	<th> @lang('messages.action')</th>
		            	<th class="col-md-2">
		            		@lang('project::lang.project')
		            	</th>
		                <th class="col-md-3">
		                	@lang('project::lang.subject')
		                </th>
		                <th class="col-md-2">
		                	@lang('project::lang.assigned_to')
		                </th>
		                <th> @lang('project::lang.priority')</th>
		                <th> @lang('business.start_date')</th>
		                <th>@lang('project::lang.due_date')</th>
		                <th>@lang('sale.status')</th>
		                <th> @lang('project::lang.assigned_by')</th>
		                <th>@lang('project::lang.task_custom_field_1')</th>
		                <th>@lang('project::lang.task_custom_field_2')</th>
		                <th>@lang('project::lang.task_custom_field_3')</th>
		                <th>@lang('project::lang.task_custom_field_4')</th>
		            </tr>
		        </thead>
		    </table>
		</div>
		<div class="custom-kanban-board hide">
		    <div class="page">
		        <div class="main">
		            <div class="meta-tasks-wrapper">
		                <div id="myKanban" class="meta-tasks"></div>
		            </div>
		        </div>
		    </div>
		</div>
	@endcomponent
</section>
<div class="modal fade project_task_model" tabindex="-1" role="dialog"></div>
<div class="modal fade view_project_task_model" tabindex="-1" role="dialog"></div>
<link rel="stylesheet" href="{{ asset('modules/project/sass/project.css?v=' . $asset_v) }}">
@endsection
@section('javascript')
<script src="{{ asset('modules/project/js/project.js?v=' . $asset_v) }}"></script>
<script type="text/javascript">
	$(document).ready(function () {
		initializeMyTaskDataTable();
	});
</script>
@endsection