@extends('layouts.app')
@section('title', __('project::lang.project_report'))

@section('content')
@include('project::layouts.nav')
<section class="content-header">
	<h1>
    	@lang('project::lang.project_report')
    </h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-md-4">
			<div class="box box-solid">
				<div class="box-body text-center">
					<i class="fas fa-hourglass-half fs-20"></i> <br>
					<span class="fs-20">
						@lang('project::lang.time_log_report') <br>
						<small>@lang('project::lang.by_employees')</small>
					</span>
				</div>
				<div class="box-footer text-center">
					<a href="{{action('\Modules\Project\Http\Controllers\ReportController@getEmployeeTimeLogReport')}}" class="btn btn-block bg-navy btn-flat">
						<i class="fa fa-eye"></i>
						@lang("messages.view")
					</a>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="box box-solid">
				<div class="box-body text-center">
					<i class="fas fa-hourglass-half fs-20"></i> <br>
					<span class="fs-20">
						@lang('project::lang.time_log_report') <br>
						<small>@lang('project::lang.by_projects')</small>
					</span>
				</div>
				<div class="box-footer text-center">
					<a href="{{action('\Modules\Project\Http\Controllers\ReportController@getProjectTimeLogReport')}}" class="btn btn-block bg-navy btn-flat">
						<i class="fa fa-eye"></i>
						@lang("messages.view")
					</a>
				</div>
			</div>
		</div>
	</div>
<link rel="stylesheet" href="{{ asset('modules/project/sass/project.css?v=' . $asset_v) }}">
</section>
@endsection