<div class="col-md-3">
	<div class="form-group">
		{!! Form::label('repair_model_id', __('repair::lang.device_model') . ':') !!}
		{!! Form::select('repair_model_id', $view_data['device_models'], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('messages.all')]); !!}
	</div>
</div>