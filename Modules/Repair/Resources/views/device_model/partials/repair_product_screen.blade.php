{!! Form::hidden('has_module_data', true); !!}
<div class="col-sm-4">
	<div class="form-group">
		{!! Form::label('repair_model_id', __('repair::lang.device_model') . ':') !!}
		{!! Form::select('repair_model_id', $view_data['device_models'], !empty($product->repair_model_id) ? $product->repair_model_id : null, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')]); !!}
	</div>
</div>