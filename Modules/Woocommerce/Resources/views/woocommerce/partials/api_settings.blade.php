<div class="pos-tab-content">
    <div class="row">
    	<div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('woocommerce_app_url',  __('woocommerce::lang.woocommerce_app_url') . ':') !!}
            	{!! Form::text('woocommerce_app_url', $default_settings['woocommerce_app_url'], ['class' => 'form-control','placeholder' => __('woocommerce::lang.woocommerce_app_url')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('woocommerce_consumer_key',  __('woocommerce::lang.woocommerce_consumer_key') . ':') !!}
                {!! Form::text('woocommerce_consumer_key', $default_settings['woocommerce_consumer_key'], ['class' => 'form-control','placeholder' => __('woocommerce::lang.woocommerce_consumer_key')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('woocommerce_consumer_secret', __('woocommerce::lang.woocommerce_consumer_secret') . ':') !!}
                <input type="password" name="woocommerce_consumer_secret" value="{{$default_settings['woocommerce_consumer_secret']}}" id="woocommerce_consumer_secret" class="form-control">
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('location_id',  __('business.business_locations') . ':') !!} @show_tooltip(__('woocommerce::lang.location_dropdown_help'))
                {!! Form::select('location_id', $locations, $default_settings['location_id'], ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="checkbox">
                <label>
                    <br/>
                    {!! Form::checkbox('enable_auto_sync', 1, !empty($default_settings['enable_auto_sync']), ['class' => 'input-icheck'] ); !!} @lang('woocommerce::lang.enable_auto_sync')
                </label>
                @show_tooltip(__('woocommerce::lang.auto_sync_tooltip'))
            </div>
        </div>
    </div>
</div>