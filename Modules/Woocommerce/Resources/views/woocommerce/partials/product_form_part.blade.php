<div class="col-md-3">
	<div class="form-group">
		@php
			$is_disabled = !empty($product->woocommerce_disable_sync) ? true : false;
      if(empty($product) && !empty($duplicate_product->woocommerce_disable_sync)){
        $is_disabled = true;
      }
		@endphp
      <br>
        <label>
        	<input type="hidden" name="woocommerce_disable_sync" value="0">
          	{!! Form::checkbox('woocommerce_disable_sync', 1, $is_disabled, ['class' => 'input-icheck']); !!} <strong>@lang('woocommerce::lang.woocommerce_disable_sync')</strong>
        </label>
        @show_tooltip(__('woocommerce::lang.woocommerce_disable_sync_help'))
  	</div>
</div>