@if(count($available_shifts) > 0)
	<h4>@lang('essentials::lang.your_shifts'):</h4>
	@foreach($available_shifts as $available_shift)
		<table class="table table-striped text-center">
			<caption>
				{{ucFirst($available_shift->name)}} (<code>@lang('essentials::lang.'.$available_shift->type)</code>)
			</caption>
			<thead>
				<tr>
					<th>
						@lang('essentials::lang.start_date')
					</th>
					<th>
						@lang('essentials::lang.end_date')
					</th>
					@if($available_shift->type == 'fixed_shift')
						<th>
							@lang('essentials::lang.timing')
						</th>
					@endif
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						{{@format_date($available_shift->start_date)}}
					</td>
					<td>
						{{@format_date($available_shift->end_date)}}
					</td>
					@if($available_shift->type == 'fixed_shift')
						<td>
							{{@format_time($available_shift->start_time)}} - {{@format_time($available_shift->end_time)}}
						</td>
					@endif
				</tr>
			</tbody>
		</table>
	@endforeach
@endif