<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Essentials\Http\Controllers\EssentialsLeaveTypeController@update', [$leave_type->id]), 'method' => 'put', 'id' => 'edit_leave_type_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'essentials::lang.edit_leave_type' )</h4>
    </div>

    <div class="modal-body">
      	<div class="form-group">
        	{!! Form::label('leave_type', __( 'essentials::lang.leave_type' ) . ':*') !!}
          	{!! Form::text('leave_type', $leave_type->leave_type, ['class' => 'form-control', 'required', 'placeholder' => __( 'essentials::lang.leave_type' ) ]); !!}
      	</div>

      	<div class="form-group">
        	{!! Form::label('max_leave_count', __( 'essentials::lang.max_leave_count' ) . ':') !!}
          	{!! Form::number('max_leave_count', $leave_type->max_leave_count, ['class' => 'form-control', 'placeholder' => __( 'essentials::lang.max_leave_count' ) ]); !!}
      	</div>

        <div class="form-group">
            <strong>@lang('essentials::lang.leave_count_interval')</strong><br>
            <label class="radio-inline">
              {!! Form::radio('leave_count_interval', 'month', $leave_type->leave_count_interval == 'month'); !!} @lang('essentials::lang.current_month')
            </label>
            <label class="radio-inline">
              {!! Form::radio('leave_count_interval', 'year', $leave_type->leave_count_interval == 'year'); !!} @lang('essentials::lang.current_fy')
            </label>
            <label class="radio-inline">
              {!! Form::radio('leave_count_interval', null, empty($leave_type->leave_count_interval)); !!} @lang('lang_v1.none')
            </label>
        </div>
    </div>

    <div class="modal-footer">
      	<button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
      	<button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->