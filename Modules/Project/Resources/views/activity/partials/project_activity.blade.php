<table class="table table-hover">
	<caption>
		@lang('project::lang.updated_values')
	</caption>
    <thead>
    	<tr>
    		<th class="col-md-2">
    			@lang('project::lang.field')
    		</th>
    		<th class="col-md-5">
    			@lang('project::lang.old_value')
    		</th>
    		<th class="col-md-5">
    			@lang('project::lang.new_value')
    		</th>
        </tr>
    </thead>
    <tbody>
    	@foreach($activity->properties['attributes'] as $key => $value)
    	<tr>
    		@if($key == 'name' || $key == 'start_date' || $key == 'end_date' || $key == 'status' || $key == 'description')
				<td>
					{{$label [$key]}}
				</td>
				<td>
					@if($key == 'name' || $key == 'description')

						{!! $activity->properties['old'][$key] !!}

					@elseif($key == 'start_date' || $key == 'end_date')

						{{@format_date($activity->properties['old'][$key])}}

					@elseif($key == 'status')

						{{$status_and_priority[$activity->properties['old'][$key]]}}

					@endif
				</td>
				<td>
					@if($key == 'name' || $key == 'description')

						{!! $value !!}

					@elseif($key == 'start_date' || $key == 'end_date')

						{{@format_date($value)}}

					@elseif($key == 'status')
					
						{{$status_and_priority[$value]}}

					@endif
				</td>
			@endif
		</tr>	
		@endforeach
    </tbody>
</table>