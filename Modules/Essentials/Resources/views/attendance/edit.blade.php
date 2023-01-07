<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Essentials\Http\Controllers\AttendanceController@update', [$attendance->id]), 'method' => 'put', 'id' => 'attendance_form' ]) !!}

    {!! Form::hidden('employees', $attendance->employee->id, ['id' => 'employees']); !!}
    {!! Form::hidden('attendance_id', $attendance->id, ['id' => 'attendance_id']); !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'essentials::lang.edit_attendance' )</h4>
    </div>

    <div class="modal-body">
    	<div class="row">
    		<div class="form-group col-md-12">
    			<strong>@lang('essentials::lang.employees'): </strong> {{$attendance->employee->user_full_name}}
    		</div>
    		<div class="form-group col-md-6">
	        	{!! Form::label('clock_in_time', __( 'essentials::lang.clock_in_time' ) . ':*') !!}
	        	<div class="input-group date">
	        		{!! Form::text('clock_in_time', @format_datetime($attendance->clock_in_time), ['class' => 'form-control', 'placeholder' => __( 'essentials::lang.clock_in_time' ), 'readonly', 'required' ]); !!}
	        		<span class="input-group-addon"><i class="fas fa-clock"></i></span>
	        	</div>
	      	</div>
	      	<div class="form-group col-md-6">
	        	{!! Form::label('clock_out_time', __( 'essentials::lang.clock_out_time' ) . ':') !!}
	        	<div class="input-group date">
	        		{!! Form::text('clock_out_time', !empty($attendance->clock_out_time) ? @format_datetime($attendance->clock_out_time) : null, ['class' => 'form-control', 'placeholder' => __( 'essentials::lang.clock_out_time' ), 'readonly' ]); !!}
	        		<span class="input-group-addon"><i class="fas fa-clock"></i></span>
	        	</div>
	      	</div>
	      	<div class="form-group col-md-6">
	        	{!! Form::label('ip_address', __( 'essentials::lang.ip_address' ) . ':') !!}
	        	{!! Form::text('ip_address', $attendance->ip_address, ['class' => 'form-control', 'placeholder' => __( 'essentials::lang.ip_address') ]); !!}
	      	</div>
	      	<div class="form-group col-md-12">
              {!! Form::label('clock_in_note', __( 'essentials::lang.clock_in_note' ) . ':') !!}
              {!! Form::textarea('clock_in_note', $attendance->clock_in_note, ['class' => 'form-control', 'placeholder' => __( 'essentials::lang.clock_in_note'), 'rows' => 3 ]); !!}
            </div>
            <div class="form-group col-md-12">
              {!! Form::label('clock_out_note', __( 'essentials::lang.clock_out_note' ) . ':') !!}
              {!! Form::textarea('clock_out_note', $attendance->clock_out_note, ['class' => 'form-control', 'placeholder' => __( 'essentials::lang.clock_out_note'), 'rows' => 3 ]); !!}
            </div>
    	</div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->