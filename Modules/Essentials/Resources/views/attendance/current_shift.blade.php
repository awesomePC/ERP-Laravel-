@if(!empty($shift_info))
	<h4>
		{{ucfirst($shift_info->name)}}
		<small>
			(
				<code>
					@lang('essentials::lang.'.$shift_info->type)
				</code>
			)
		</small>
	</h4>
	@if($shift_info->type == 'fixed_shift')
		<b>@lang('restaurant.start_time'):</b> {{@format_time($shift_info->start_time)}} 
		<br>
		<b>@lang('restaurant.end_time'):</b> {{@format_time($shift_info->end_time)}}
	@endif
@endif