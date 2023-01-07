@forelse($schedules as $schedule)
<div class="external-event 
	@if($schedule->status == 'scheduled')
		bg-yellow
	@elseif($schedule->status == 'open')
		bg-blue
	@elseif($schedule->status == 'canceled')
		bg-red
	@elseif($schedule->status == 'completed')
		bg-green
	@else
		bg-yellow
	@endif width-100" style="display: flex;">
	<div style="width: 75%;">
		{{$schedule->title}}
		<br>
		<a href="{{action('\Modules\Crm\Http\Controllers\ScheduleController@show', ['follow-ups' => $schedule->id ])}}" class="text-white mr-8" data-toggle="tooltip" title="{{__('crm::lang.view_follow_up')}}"><i class="fas fa-eye"></i></a>
		<a href="{{action('\Modules\Crm\Http\Controllers\ScheduleLogController@create', ['schedule_id' => $schedule->id])}}" class="add-schedule-log text-white" class="text-white" data-toggle="tooltip" title="{{__('crm::lang.add_schedule_log')}}"><i class="fas fa-business-time"></i></a>
	</div>
	
</div>
@empty
	@lang('crm::lang.no_schedule_for_today')
@endforelse