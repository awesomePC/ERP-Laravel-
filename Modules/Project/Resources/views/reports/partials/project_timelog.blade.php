@php
$record_not_available = true;
@endphp
@foreach($projects as $project)
	@if($project->timeLogs->count() > 0)
		@php
			$record_not_available = false;
		@endphp
		<div class="box box-solid box-success">
			<div class="box-body">
				<div class="table-responsive">
					<caption>
						<span class="font-17 text-bold">
							{{$project->name}}:
						</span>
					</caption>
					<ol>
						<table class="table table-striped">
							<thead>
								<tr>
									<th>@lang('project::lang.task')</th>
									<th>@lang('role.user')</th>
									<th>@lang('project::lang.start_date_time')</th>
									<th>@lang('project::lang.end_date_time')</th>
									<th>@lang('project::lang.work_hour')</th>
									<th>@lang('brand.note')</th>
								</tr>
							</thead>
							<tbody>
								@php
		    						$total_sec = 0;
		    					@endphp
								@foreach($project->timeLogs as $timeLog)
									@php
			    						$start_datetime = \Carbon::parse($timeLog->start_datetime);
	        							$end_datetime = \Carbon::parse($timeLog->end_datetime);
	        							$second = $start_datetime->diffInSeconds($end_datetime, true);
	        							$total_sec += $second;
			    					@endphp
									<tr>
										<td>
											@if(isset($timeLog->task->subject))
												{{$timeLog->task->subject}}
												<small>
													<code>
														{{$timeLog->task->task_id}}
													</code>
												</small>
											@endif
										</td>
										<td>
											{{$timeLog->user->user_full_name}}
										</td>
										<td>
											{{@format_datetime($timeLog->start_datetime)}}
										</td>
										<td>
											{{@format_datetime($timeLog->end_datetime)}}
										</td>
										<td>
											{{$start_datetime->diffForHumans($end_datetime, true)}}
										</td>
										<td>
											{{$timeLog->note}}
										</td>
									</tr>
								@endforeach
							</tbody>
							<tfoot>
				    				<tr class="bg-gray">
				    					<td colspan="4"></td>
				    					<td>
				    						@php
												$hours = floor($total_sec / 3600);
												$minutes = floor(($total_sec / 60) % 60);
											@endphp
											{{sprintf('%02d:%02d', $hours, $minutes)}}
				    					</td>
				    					<td></td>
				    				</tr>
				    			</tfoot>
						</table>
					</ol>
				</div>
			</div>
		</div>
	@endif
@endforeach
@if($record_not_available)
	<div class="callout callout-info">
        <h4>
        	<i class="fa fa-warning"></i>
        	@lang('project::lang.no_record_found')
        </h4>
    </div>
@endif