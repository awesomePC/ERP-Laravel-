@extends('layouts.app')

@section('title', __('crm::lang.schedule'))

@section('content')
@include('crm::layouts.nav')
<!-- Content Header (Page header) -->
<section class="content-header no-print">
	<h1>
   		@lang('crm::lang.schedule') 
        <code>({{ $schedule->title }})</code>
	</h1>
</section>

<section class="content no-print">
	<div class="row">
		<div class="col-md-12">
			<div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
					<li class="active">
						<a href="#schedule_info" data-toggle="tab" aria-expanded="true">
							@lang('crm::lang.schedule_info')
						</a>
					</li>
					<li>
						<a href="#schedule_log" data-toggle="tab" aria-expanded="true">
							@lang('crm::lang.schedule_log')
						</a>
					</li>
				</ul>
				<div class="tab-content">
                    <div class="tab-pane active" id="schedule_info">
                    	@includeIf('crm::schedule.partial.schedule_info')
                    </div>
                    <div class="tab-pane" id="schedule_log">
                    	@includeIf('crm::schedule_log.index')
                    	<input type="hidden" name="schedule_id" id="schedule_id" value="{{$schedule->id}}">
                    </div>
                </div>
            </div>
		</div>
	</div>
</section>
<div class="modal fade edit_schedule" tabindex="-1" role="dialog"></div>
<div class="modal fade schedule_log_modal" tabindex="-1" role="dialog"></div>
@endsection
@section('javascript')
	<script src="{{ asset('modules/crm/js/crm.js?v=' . $asset_v) }}"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			getScheduleLog();
		});
	</script>
@endsection