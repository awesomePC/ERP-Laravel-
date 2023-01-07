<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Essentials\Http\Controllers\EssentialsHolidayController@update', [$holiday->id]), 'method' => 'put', 'id' => 'add_holiday_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'essentials::lang.edit_holiday' )</h4>
    </div>

    <div class="modal-body">
    	<div class="row">
    		<div class="form-group col-md-12">
	        	{!! Form::label('name', __( 'lang_v1.name' ) . ':*') !!}
	          	{!! Form::text('name', $holiday->name, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.name' ), 'required']); !!}
	      	</div>

	      	<div class="form-group col-md-6">
	        	{!! Form::label('start_date', __( 'essentials::lang.start_date' ) . ':*') !!}
	        	<div class="input-group data">
	        		{!! Form::text('start_date', @format_date($holiday->start_date), ['class' => 'form-control', 'placeholder' => __( 'essentials::lang.start_date' ), 'readonly' ]); !!}
	        		<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
	        	</div>
	      	</div>

	      	<div class="form-group col-md-6">
	        	{!! Form::label('end_date', __( 'essentials::lang.end_date' ) . ':*') !!}
		        	<div class="input-group data">
		          	{!! Form::text('end_date', @format_date($holiday->end_date), ['class' => 'form-control', 'placeholder' => __( 'essentials::lang.end_date' ), 'readonly', 'required' ]); !!}
		          	<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
	        	</div>
	      	</div>

	      	<div class="form-group col-md-12">
	        	{!! Form::label('location_id', __( 'business.business_location' ) . ':') !!}
	          	{!! Form::select('location_id', $locations, $holiday->location_id, ['class' => 'form-control select2', 'placeholder' => __( 'lang_v1.all' ) ]); !!}
	      	</div>

	      	<div class="form-group col-md-12">
	        	{!! Form::label('note', __( 'brand.note' ) . ':') !!}
	          	{!! Form::textarea('note', $holiday->note, ['class' => 'form-control', 'placeholder' => __( 'brand.note' ), 'rows' => 3 ]); !!}
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