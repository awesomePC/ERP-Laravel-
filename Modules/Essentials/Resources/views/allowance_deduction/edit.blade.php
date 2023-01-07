<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Essentials\Http\Controllers\EssentialsAllowanceAndDeductionController@update', $allowance->id), 'method' => 'put', 'id' => 'add_allowance_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'essentials::lang.edit_pay_component' )</h4>
    </div>

    <div class="modal-body">
    	<div class="row">
    		<div class="form-group col-md-12">
	        	{!! Form::label('description', __( 'lang_v1.description' ) . ':*') !!}
	          	{!! Form::text('description', $allowance->description, ['class' => 'form-control', 'required', 'placeholder' => __( 'lang_v1.description' ) ]); !!}
	      	</div>

	      	<div class="form-group col-md-12">
	        	{!! Form::label('type', __( 'lang_v1.type' ) . ':*') !!}
	          	{!! Form::select('type', ['allowance' => __('essentials::lang.allowance'), 'deduction' => __('essentials::lang.deduction')], $allowance->type, ['class' => 'form-control', 'required' ]); !!}
	      	</div>

	      	<div class="form-group col-md-12">
	        	{!! Form::label('employees', __('essentials::lang.employee') . ':') !!}
	          	{!! Form::select('employees[]', $users, $selected_users, ['class' => 'form-control select2', 'multiple' ]); !!}
	      	</div>

	      	<div class="form-group col-md-6">
	        	{!! Form::label('amount_type', __( 'essentials::lang.amount_type' ) . ':*') !!}
	          	{!! Form::select('amount_type', ['fixed' => __('lang_v1.fixed'), 'percent' => __('lang_v1.percentage')], $allowance->amount_type, ['class' => 'form-control', 'required' ]); !!}
	      	</div>
	      	
	      	<div class="form-group col-md-6">
	        	{!! Form::label('amount', __( 'sale.amount' ) . ':*') !!}
	          	{!! Form::text('amount', @num_format($allowance->amount), ['class' => 'form-control input_number', 'placeholder' => __( 'sale.amount' ), 'required' ]); !!}
	      	</div>

	      	<div class="form-group col-md-12">
	        	{!! Form::label('applicable_date', __( 'essentials::lang.applicable_date' ) . ':') !!} @show_tooltip(__('essentials::lang.applicable_date_help'))
	        	<div class="input-group data">
	        		{!! Form::text('applicable_date', $applicable_date, ['class' => 'form-control', 'placeholder' => __( 'essentials::lang.applicable_date' ), 'readonly' ]); !!}
	        		<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
	        	</div>
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