<div class="modal fade" id="payroll_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
	<div class="modal-dialog" role="document">
	  <div class="modal-content">

	    {!! Form::open(['url' => action('\Modules\Essentials\Http\Controllers\PayrollController@create'), 'method' => 'get', 'id' => 'add_payroll_step1' ]) !!}

	    <div class="modal-body">
	    	<div class="form-group">
	        	{!! Form::label('primary_work_location', __( 'business.location' ) . ':*') !!}
	          	{!! Form::select('primary_work_location', $locations, null, ['class' => 'form-control select2', 'style' => 'width: 100%;', 'id' => 'primary_work_location']); !!}
	      	</div>
	      	<div class="form-group">
	        	{!! Form::label('employee_ids', __( 'essentials::lang.employee' ) . ':*') !!}
	        	<button type="button" class="btn btn-primary btn-xs select-all">
                    @lang('lang_v1.select_all')
                </button>
                <button type="button" class="btn btn-primary btn-xs deselect-all">
                    @lang('lang_v1.deselect_all')
                </button>
	          	{!! Form::select('employee_ids[]', $employees, null, ['class' => 'form-control select2', 'required', 'style' => 'width: 100%;', 'multiple', 'id' => 'employee_ids']); !!}
	      	</div>

	      	<div class="form-group">
	      		{!! Form::label('month_year', __( 'essentials::lang.month_year' ) . ':*') !!}
	      		<div class="input-group">
	          		{!! Form::text('month_year', null, ['class' => 'form-control', 'placeholder' => __( 'essentials::lang.month_year' ), 'required', 'readonly' ]); !!}
	          		<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
	      		</div>
	      	</div>
	    </div>

	    <div class="modal-footer">
	      <button type="submit" class="btn btn-primary">@lang( 'essentials::lang.proceed' )</button>
	      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
	    </div>

	    {!! Form::close() !!}

	  </div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div>