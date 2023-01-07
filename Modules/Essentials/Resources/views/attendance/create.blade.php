<div class="modal-dialog modal-xl" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Essentials\Http\Controllers\AttendanceController@store'), 'method' => 'post', 'id' => 'attendance_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'essentials::lang.add_latest_attendance' )</h4>
    </div>

    <div class="modal-body">
    	<div class="row">
    		<div class="form-group col-md-12">
		        {!! Form::label('employee', __('essentials::lang.select_employee') . ':') !!}
		        {!! Form::select('employee', $employees, null, ['class' => 'form-control select2', 'style' => 'width: 100%;', 'id' => 'select_employee', 'placeholder' => __('essentials::lang.select_employee') ]); !!}
    		</div>
    		<table class="table" id="employee_attendance_table">
    			<thead>
    				<th width="10%">@lang('essentials::lang.employee')</th>
    				<th width="15%">@lang('essentials::lang.clock_in_time')</th>
    				<th width="15%">@lang('essentials::lang.clock_out_time')</th>
    				<th width="15%">@lang('essentials::lang.shift')</th>
    				<th width="12%">@lang('essentials::lang.ip_address')</th>
    				<th width="15%">@lang('essentials::lang.clock_in_note')</th>
    				<th width="15%">@lang('essentials::lang.clock_out_note')</th>
    				<th width="3%">&nbsp;</th>
    			</thead>
    			<tbody>
    			</tbody>
    		</table>
    	</div>
    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->