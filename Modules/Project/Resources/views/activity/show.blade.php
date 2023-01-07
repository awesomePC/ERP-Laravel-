
	<!-- timeline time label -->
	@php
		$created_at = null;
		$icon_color = [
			'created' => 'bg-green',
			'updated' => 'bg-blue',
			'deleted' => 'bg-red',
			'settings_updated' => 'bg-blue'
		];

		$label = [
			'subject' => __('project::lang.subject'),
			'description' => __('lang_v1.description'),
			'start_date' => __('business.start_date'),
			'due_date' => __('project::lang.due_date'),
			'priority' => __('project::lang.priority'),
			'status' => __('sale.status'),
			'name' => __('messages.name'),
			'end_date' => __('project::lang.end_date'),
		];

		$status_and_priority = [
			'completed' => __('project::lang.completed'),
			'cancelled' => __('project::lang.cancelled'),
			'on_hold' => __('project::lang.on_hold'),
			'in_progress' => __('project::lang.in_progress'),
			'not_started' => __('project::lang.not_started'),
			'low' => __('project::lang.low'),
			'medium' => __('project::lang.medium'),
			'high' => __('project::lang.high'),
			'urgent' => __('project::lang.urgent'),
		];
	@endphp
	@foreach($activities as $activity)

		@if($created_at != $activity->created_at->format('Y-m-d'))
			<li class="time-label">
				<span class="bg-red">
					{{@format_date($activity->created_at)}}
				</span>
			</li>
		@endif

		<!-- /.timeline-label -->
		<!-- timeline item -->
		<li>
			<!-- timeline icon -->
			@if($activity->subject_type == 'Modules\Project\Entities\Project')
				<i class="fas fa fa-check-circle
					{{$icon_color[$activity->description]}}"></i>
			@elseif($activity->subject_type == 'Modules\Project\Entities\ProjectTask')
				<i class="fa fa-tasks {{$icon_color[$activity->description]}}"></i>
			@elseif($activity->subject_type == 'App\DocumentAndNote')
				<i class="fas fa fa-images {{$icon_color[$activity->description]}}"></i>
			@elseif($activity->subject_type == 'Modules\Project\Entities\ProjectTimeLog')
				<i class="fas fa fa-clock {{$icon_color[$activity->description]}}"></i>
			@endif
			<div class="timeline-item">
				<span class="time">
					<i class="fas fa-clock"></i>
					{{@format_time($activity->created_at)}}
				</span>
				<h3 class="timeline-header timeline-body-custom-color">
					@if(($activity->subject_type == 'Modules\Project\Entities\Project') && $activity->description == 'settings_updated')

						@lang('project::lang.project_settings_updated', [
							'name' => $activity->causer->user_full_name
						])
					@elseif($activity->subject_type == 'Modules\Project\Entities\Project')

						@lang('project::lang.project_activity', [
							'name' => $activity->causer->user_full_name ,
							'description' => $activity->description
						])
					@elseif($activity->subject_type == 'Modules\Project\Entities\ProjectTask')

						@lang('project::lang.project_task_activity', [
							'name' => $activity->causer->user_full_name ,
							'description' => $activity->description
						])
					@elseif($activity->subject_type == 'App\DocumentAndNote')
						
						@lang('project::lang.project_note_activity', [
							'name' => $activity->causer->user_full_name ,
							'description' => $activity->description
						])

					@elseif($activity->subject_type == 'Modules\Project\Entities\ProjectTimeLog')
						
						@lang('project::lang.project_timelog_activity', [
							'name' => $activity->causer->user_full_name ,
							'description' => $activity->description
						])

					@endif
				</h3>

				<div class="timeline-body timeline-body-custom-color">
					@if($activity->subject_type == 'Modules\Project\Entities\Project')

						@if($activity->description == 'created')
							<code>{{$activity->properties['attributes']['name']}}</code>
							<!-- check if updated value's key exist or not then create table -->
						@elseif(($activity->description == 'updated') && 
						(
							array_key_exists('name', $activity->properties['attributes']) || 
							array_key_exists('status', $activity->properties['attributes']) || array_key_exists('start_date', $activity->properties['attributes']) || array_key_exists('end_date', $activity->properties['attributes']) || array_key_exists('description', $activity->properties['attributes'])
						))
							<div class="table-responsive">
								@includeIf('project::activity.partials.project_activity')
							</div>
						@elseif($activity->description == 'settings_updated')
						@endif

					@elseif($activity->subject_type == 'Modules\Project\Entities\ProjectTask')

						@if($activity->description == 'created')
							<a data-href='{{action("\Modules\Project\Http\Controllers\TaskController@show", ["id" => $activity->subject->id, "project_id" => $activity->subject->project_id])}}' class="cursor-pointer view_a_project_task text-black">
								{{$activity->properties['attributes']['subject']}}
								<code>
									{{$activity->properties['attributes']['task_id']}}	
								</code>
							</a>
						@elseif($activity->description == 'deleted')
							<span>
								{{$activity->properties['attributes']['subject']}}
								<code>
									{{$activity->properties['attributes']['task_id']}}	
								</code>
							</span>
						@elseif($activity->description == 'updated')
							<a data-href='{{action("\Modules\Project\Http\Controllers\TaskController@show", ["id" => $activity->subject->id, "project_id" => $activity->subject->project_id])}}' class="cursor-pointer view_a_project_task text-black">
								{{$activity->subject->subject}}
								<code>
									{{$activity->subject->task_id}}
								</code>
							</a><br>
							<!-- check if updated value's key exist or not then create table -->
							@if(
								array_key_exists('subject', $activity->properties['attributes']) ||
								array_key_exists('start_date', $activity->properties['attributes']) ||
								array_key_exists('due_date', $activity->properties['attributes']) ||
								array_key_exists('priority', $activity->properties['attributes']) || 
								array_key_exists('description', $activity->properties['attributes']) ||
								array_key_exists('status', $activity->properties['attributes'])
							)
								<div class="table-responsive">
									@includeIf('project::activity.partials.task_activity')
								</div>
							@endif
						@endif

					@elseif($activity->subject_type == 'App\DocumentAndNote')

						@if($activity->description == 'created')
							
							<a data-href='{{action("DocumentAndNoteController@show", ["id" => $activity->subject->id, "notable_id" => $activity->subject->notable_id, "notable_type" => $activity->subject->notable_type])}}' class="cursor-pointer view_a_docs_note text-black">
							    <code>
							    	{{$activity->properties['attributes']['heading']}}
							    </code>
							</a>
						@elseif($activity->description == 'updated')
							
							<a data-href='{{action("DocumentAndNoteController@show", ["id" => $activity->subject->id, "notable_id" => $activity->subject->notable_id, "notable_type" => $activity->subject->notable_type])}}' class="cursor-pointer view_a_docs_note text-black">
							    <code>{{$activity->subject->heading}}</code>
							</a>
						@endif

					@elseif($activity->subject_type == 'Modules\Project\Entities\ProjectTimeLog')

						@if($activity->description == 'created')
							<b>@lang('project::lang.work_hour'):</b>
							<span>
								@includeIf('project::activity.partials.time_log')
							</span> <br>
							{!! $activity->properties['attributes']['note'] !!}
						@elseif($activity->description == 'updated')
							<b>@lang('project::lang.work_hour'):</b>
							<span>
								@includeIf('project::activity.partials.time_log')
							</span> <br>
							{!! $activity->subject->note !!}
						@endif

					@endif
				</div>
			</div>
		</li>

		@php
			$created_at = $activity->created_at->format('Y-m-d');
		@endphp

	@endforeach
	<!-- END timeline item -->
@if($activities->nextPageUrl())
	<li class="timeline-lode-more-btn">
		<a data-href="{{$activities->nextPageUrl()}}" class="btn btn-block btn-sm btn-info load_more_activities">
			@lang('project::lang.load_more')
		</a>
	</li>
@endif