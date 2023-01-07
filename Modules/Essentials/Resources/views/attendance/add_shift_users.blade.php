<div class="modal-dialog" role="document">
  	<div class="modal-content">
  		{!! Form::open(['url' => action('\Modules\Essentials\Http\Controllers\ShiftController@postAssignUsers'), 'method' => 'post', 'id' => 'add_user_shift_form' ]) !!}
  		<div class="modal-header">
	      	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      	<h4 class="modal-title">
	      		@lang( 'essentials::lang.assign_users' )
	      		(
	      			{{$shift->name}}
	      			@if($shift->type == 'fixed_shift')
	      			: {{@format_time($shift->start_time)}} - {{@format_time($shift->end_time)}}
	      			@endif
	      		)
	      	</h4>
	    </div>
	    <div class="modal-body">
	    	{!! Form::hidden('shift_id', $shift->id); !!}
	    	<table class="table table-condensed">
	    		<thead>
	    			<tr>
	    				<th>&nbsp;</th>
	    				<th>@lang('report.user')</th>
	    				<th>@lang('business.start_date')</th>
	    				<th>@lang('essentials::lang.end_date')</th>
	    			</tr>
	    		</thead>
	    		<tbody>
	    			@foreach($users as $key => $value)
	    				<tr>
	    					<td>{!! Form::checkbox('user_shift[' . $key . '][is_added]', 1, array_key_exists($key, $user_shifts), ['id' => 'user_check_' . $key ]); !!}</td>
	    					<td>{{$value}}</td>
	    					<td>
					        	<div class="input-group date">
					        		{!! Form::text('user_shift[' . $key . '][start_date]', !empty($user_shifts[$key]['start_date']) ? $user_shifts[$key]['start_date'] : null, ['class' => 'form-control date_picker', 'placeholder' => __( 'business.start_date' ), 'readonly', 'id' => 'user_shift[' . $key . '][start_date]']); !!}
					        		<span class="input-group-addon"><i class="fas fa-clock"></i></span>
					        	</div>
					        </td>
					        <td>
					        	<div class="input-group date">
					        		{!! Form::text('user_shift[' . $key . '][end_date]', !empty($user_shifts[$key]['end_date']) ? $user_shifts[$key]['end_date'] : null, ['class' => 'form-control date_picker', 'placeholder' => __( 'essentials::lang.end_date' ), 'readonly' ]); !!}
					        		<span class="input-group-addon"><i class="fas fa-clock"></i></span>
					        	</div>
					        </td>
	    				</tr>
	    			@endforeach
	    		</tbody>
	    	</table>
	    </div>
	    <div class="modal-footer">
	      	<button type="submit" class="btn btn-primary">@lang( 'messages.submit' )</button>
	      	<button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
	    </div>
	    {!! Form::close() !!}
  	</div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$('#add_user_shift_form').validate({
			rules: {
				@foreach($users as $key => $value)
				'user_shift[{{$key}}][start_date]': {
					required: function(element){
		            	return $('#user_check_{{$key}}').is(":checked");
		        	}
		    	},
				@endforeach
			}
		});
	});
</script>