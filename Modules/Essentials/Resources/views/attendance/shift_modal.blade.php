<div class="modal-dialog" role="document">
  	<div class="modal-content">
  		{!! Form::open(['url' => empty($shift) ? action('\Modules\Essentials\Http\Controllers\ShiftController@store') : action('\Modules\Essentials\Http\Controllers\ShiftController@update', [$shift->id]), 'method' => empty($shift) ? 'post' : 'put', 'id' => 'add_shift_form' ]) !!}
  		<div class="modal-header">
	      	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      	<h4 class="modal-title">@lang( 'essentials::lang.add_shift' )</h4>
	    </div>
	    <div class="modal-body">
	    	<div class="form-group">
	        	{!! Form::label('name', __( 'user.name' ) . ':*') !!}
	        	{!! Form::text('name', !empty($shift->name) ? $shift->name : null, ['class' => 'form-control', 'placeholder' => __( 'user.name'), 'required']); !!}
	      	</div>
	      	<div class="form-group">
	        	{!! Form::label('type', __('essentials::lang.shift_type') . ':*') !!} @show_tooltip(__('essentials::lang.shift_type_tooltip'))
	        	{!! Form::select('type', ['fixed_shift' => __('essentials::lang.fixed_shift'), 'flexible_shift' => __('essentials::lang.flexible_shift')],  !empty($shift->type) ? $shift->type : null, ['class' => 'form-control select2', 'required', 'id' => 'shift_type']); !!}
	      	</div>
	      	<div class="form-group time_div">
	        	{!! Form::label('start_time', __( 'restaurant.start_time' ) . ':*') !!}
	        	<div class="input-group date">
	        		{!! Form::text('start_time', !empty($shift->start_time) ? @format_time($shift->start_time) : null, ['class' => 'form-control', 'placeholder' => __( 'restaurant.start_time' ), 'readonly', 'required' ]); !!}
	        		<span class="input-group-addon"><i class="fas fa-clock"></i></span>
	        	</div>
	      	</div>
	      	<div class="form-group time_div">
	        	{!! Form::label('end_time', __( 'restaurant.end_time' ) . ':*') !!}
	        	<div class="input-group date">
	        		{!! Form::text('end_time', !empty($shift->end_time) ? @format_time($shift->end_time) : null, ['class' => 'form-control', 'placeholder' => __( 'restaurant.end_time' ), 'readonly', 'required']); !!}
	        		<span class="input-group-addon"><i class="fas fa-clock"></i></span>
	        	</div>
	      	</div>
	      	<div class="form-group">
	        	{!! Form::label('holidays', __( 'essentials::lang.holiday' ) . ':') !!}
	        	{!! Form::select('holidays[]', $days,  !empty($shift->holidays) ? $shift->holidays : null, ['class' => 'form-control select2', 'multiple' ]); !!}
	      	</div>
            <div class="form-group">
                <label>
                	{!! Form::checkbox('is_allowed_auto_clockout', 1, !empty($shift->is_allowed_auto_clockout) ? 1 : 0, ['id' => 'is_allowed_auto_clockout']); !!}
                	@lang('essentials::lang.allow_auto_clockout')
                </label>
                @show_tooltip(__('essentials::lang.allow_auto_clockout_tooltip'))
            </div>
    		<div class="form-group enable_auto_clock_out_time" style="display: none;">
    			{!! Form::label('auto_clockout_time', __( 'essentials::lang.auto_clockout_time' ) . ':*') !!}
	        	<div class="input-group date">
	        		{!! Form::text('auto_clockout_time', !empty($shift->auto_clockout_time) ? @format_time($shift->auto_clockout_time) : null, ['class' => 'form-control', 'placeholder' => __( 'essentials::lang.auto_clockout_time' ), 'readonly', 'required']); !!}
	        		<span class="input-group-addon"><i class="fas fa-clock"></i></span>
	        	</div>
    		</div>
	    </div>
	    <div class="modal-footer">
	      	<button type="submit" class="btn btn-primary">@lang( 'messages.submit' )</button>
	      	<button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
	    </div>
	    {!! Form::close() !!}
  	</div>
</div>