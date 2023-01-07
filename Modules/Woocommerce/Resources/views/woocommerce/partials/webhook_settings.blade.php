<div class="pos-tab-content">
    <div class="row">
        <div class="col-xs-12">
            <h4>@lang('woocommerce::lang.order_created')</h4>
        </div>
    	<div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('woocommerce_wh_oc_secret',  __('woocommerce::lang.webhook_secret') . ':') !!}
            	{!! Form::text('woocommerce_wh_oc_secret', !empty($business->woocommerce_wh_oc_secret) ? $business->woocommerce_wh_oc_secret : null, ['class' => 'form-control','placeholder' => __('woocommerce::lang.webhook_secret')]); !!}
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group">
                <strong>@lang('woocommerce::lang.webhook_delivery_url'):</strong>
                <p>{{action('\Modules\Woocommerce\Http\Controllers\WoocommerceWebhookController@orderCreated', ['business_id' => session()->get('business.id')])}}</p>
            </div>
        </div>

        <div class="col-xs-12">
            <h4>@lang('woocommerce::lang.order_updated')</h4>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('woocommerce_wh_ou_secret',  __('woocommerce::lang.webhook_secret') . ':') !!}
                {!! Form::text('woocommerce_wh_ou_secret', !empty($business->woocommerce_wh_oc_secret) ? $business->woocommerce_wh_ou_secret : null, ['class' => 'form-control','placeholder' => __('woocommerce::lang.webhook_secret')]); !!}
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group">
                <strong>@lang('woocommerce::lang.webhook_delivery_url'):</strong>
                <p>{{action('\Modules\Woocommerce\Http\Controllers\WoocommerceWebhookController@orderUpdated', ['business_id' => session()->get('business.id')])}}</p>
            </div>
        </div>

        <div class="col-xs-12">
            <h4>@lang('woocommerce::lang.order_deleted')</h4>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('woocommerce_wh_od_secret',  __('woocommerce::lang.webhook_secret') . ':') !!}
                {!! Form::text('woocommerce_wh_od_secret', !empty($business->woocommerce_wh_oc_secret) ? $business->woocommerce_wh_od_secret : null, ['class' => 'form-control','placeholder' => __('woocommerce::lang.webhook_secret')]); !!}
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group">
                <strong>@lang('woocommerce::lang.webhook_delivery_url'):</strong>
                <p>{{action('\Modules\Woocommerce\Http\Controllers\WoocommerceWebhookController@orderDeleted', ['business_id' => session()->get('business.id')])}}</p>
            </div>
        </div>

        <div class="col-xs-12">
            <h4>@lang('woocommerce::lang.order_restored')</h4>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('woocommerce_wh_or_secret',  __('woocommerce::lang.webhook_secret') . ':') !!}
                {!! Form::text('woocommerce_wh_or_secret', !empty($business->woocommerce_wh_oc_secret) ? $business->woocommerce_wh_or_secret : null, ['class' => 'form-control','placeholder' => __('woocommerce::lang.webhook_secret')]); !!}
            </div>
        </div>
        <div class="col-xs-8">
            <div class="form-group">
                <strong>@lang('woocommerce::lang.webhook_delivery_url'):</strong>
                <p>{{action('\Modules\Woocommerce\Http\Controllers\WoocommerceWebhookController@orderRestored', ['business_id' => session()->get('business.id')])}}</p>
            </div>
        </div>

    </div>
</div>