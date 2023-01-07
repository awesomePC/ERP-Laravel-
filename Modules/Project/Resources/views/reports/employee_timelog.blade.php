@extends('layouts.app')
@section('title', __('project::lang.project_report'))

@section('content')
@include('project::layouts.nav')
<section class="content-header">
    <h1>
        @lang('report.reports')
        <small>
            @lang('project::lang.time_logs') @lang('project::lang.by_employees')
        </small>
    </h1>
</section>
<section class="content">
    @component('components.filters', ['title' => __('report.filters')])
		<div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('employee_timelog_report_user_id', __('role.user') .':') !!}
                   {!! Form::select('user_id[]', $employees, null, ['class' => 'form-control select2', 'id' => 'employee_timelog_report_user_id', 'multiple', 'style' => 'width: 100%;']); !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('employee_timelog_report_project_id', __('project::lang.project') . ':') !!}
                    {!! Form::select('project_id[]', $projects, null, ['class' => 'form-control select2', 'id' => 'employee_timelog_report_project_id', 'multiple', 'style' => 'width: 100%;']); !!}
                </div>    
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('employee_timelog_report_daterange', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'employee_timelog_report_daterange', 'readonly']); !!}
                </div>
            </div>
        </div>
    @endcomponent
    <div class="box box-solid">
    	<div class="box-body employee_time_logs_report">
    	</div>
    </div>
</section>
@endsection
@section('javascript')
<script src="{{ asset('modules/project/js/project.js?v=' . $asset_v) }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        getEmployeeTimeLogReport();
    });
</script>
@endsection