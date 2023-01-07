<div class="pos-tab-content">
    <div class="row">
        <div class="col-xs-6">
            <div class="form-group">
                <label>
                {!! Form::checkbox('enable_offline_payment', 1,!empty($settings["enable_offline_payment"]), 
                [ 'class' => 'input-icheck']); !!}
                @lang('superadmin::lang.enable_offline_payment')
                </label>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                {!! Form::label('offline_payment_details', __('superadmin::lang.offline_payment_details') . ':') !!}
                @show_tooltip(__('superadmin::lang.offline_payment_details_tooltip'))
                {!! Form::textarea('offline_payment_details', !empty($settings["offline_payment_details"]) ? $settings["offline_payment_details"] : null, ['class' => 'form-control','placeholder' => __('superadmin::lang.offline_payment_details'), 'rows' => 3]); !!}
            </div>
        </div>
    </div>
    <div class="row">
    	<h4>Stripe:</h4>
    	<div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('STRIPE_PUB_KEY', __('superadmin::lang.stripe_pub_key') . ':') !!}
            	{!! Form::text('STRIPE_PUB_KEY', $default_values['STRIPE_PUB_KEY'], ['class' => 'form-control','placeholder' => __('superadmin::lang.stripe_pub_key')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('STRIPE_SECRET_KEY', __('superadmin::lang.stripe_secret_key') . ':') !!}
            	{!! Form::text('STRIPE_SECRET_KEY', $default_values['STRIPE_SECRET_KEY'], ['class' => 'form-control','placeholder' => __('superadmin::lang.stripe_secret_key')]); !!}
            </div>
        </div>

        <div class="clearfix"></div>
        
        <h4>Paypal:</h4>
        <div class="col-xs-6">
            <div class="form-group">
            	{!! Form::label('PAYPAL_MODE', __('superadmin::lang.paypal_mode') . ':') !!}
            	{!! Form::select('PAYPAL_MODE',['live' => 'Live', 'sandbox' => 'Sandbox'],  $default_values['PAYPAL_MODE'], ['class' => 'form-control','placeholder' => __('messages.please_select')]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('PAYPAL_SANDBOX_API_USERNAME', __('superadmin::lang.paypal_sandbox_api_username') . ':') !!}
            	{!! Form::text('PAYPAL_SANDBOX_API_USERNAME', $default_values['PAYPAL_SANDBOX_API_USERNAME'], ['class' => 'form-control','placeholder' => __('superadmin::lang.paypal_sandbox_api_username')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('PAYPAL_SANDBOX_API_PASSWORD', __('superadmin::lang.paypal_sandbox_api_password') . ':') !!}
            	{!! Form::text('PAYPAL_SANDBOX_API_PASSWORD', $default_values['PAYPAL_SANDBOX_API_PASSWORD'], ['class' => 'form-control','placeholder' => __('superadmin::lang.paypal_sandbox_api_password')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('PAYPAL_SANDBOX_API_SECRET', __('superadmin::lang.paypal_sandbox_api_secret') . ':') !!}
            	{!! Form::text('PAYPAL_SANDBOX_API_SECRET', $default_values['PAYPAL_SANDBOX_API_SECRET'], ['class' => 'form-control','placeholder' => __('superadmin::lang.paypal_sandbox_api_secret')]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('PAYPAL_LIVE_API_USERNAME', __('superadmin::lang.paypal_live_api_username') . ':') !!}
            	{!! Form::text('PAYPAL_LIVE_API_USERNAME', $default_values['PAYPAL_LIVE_API_USERNAME'], ['class' => 'form-control','placeholder' => __('superadmin::lang.paypal_live_api_username')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('PAYPAL_LIVE_API_PASSWORD', __('superadmin::lang.paypal_live_api_password') . ':') !!}
            	{!! Form::text('PAYPAL_LIVE_API_PASSWORD', $default_values['PAYPAL_LIVE_API_PASSWORD'], ['class' => 'form-control','placeholder' => __('superadmin::lang.paypal_live_api_password')]); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
            	{!! Form::label('PAYPAL_LIVE_API_SECRET', __('superadmin::lang.paypal_live_api_secret') . ':') !!}
            	{!! Form::text('PAYPAL_LIVE_API_SECRET', $default_values['PAYPAL_LIVE_API_SECRET'], ['class' => 'form-control','placeholder' => __('superadmin::lang.paypal_live_api_secret')]); !!}
            </div>
        </div>

        <div class="clearfix"></div>
        
        <h4>Razorpay: <small>(For INR India)</small></h4>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('RAZORPAY_KEY_ID', 'Key ID:') !!}
                {!! Form::text('RAZORPAY_KEY_ID', $default_values['RAZORPAY_KEY_ID'], ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('RAZORPAY_KEY_SECRET', 'Key Secret:') !!}
                {!! Form::text('RAZORPAY_KEY_SECRET', $default_values['RAZORPAY_KEY_SECRET'], ['class' => 'form-control']); !!}
            </div>
        </div>




        <div class="clearfix"></div>
        
        <h4>Pesapal: <small>(For KES currency)</small></h4>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('PESAPAL_CONSUMER_KEY', 'Consumer Key:') !!}
                {!! Form::text('PESAPAL_CONSUMER_KEY', $default_values['PESAPAL_CONSUMER_KEY'], ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('PESAPAL_CONSUMER_SECRET', 'Consumer Secret:') !!}
                {!! Form::text('PESAPAL_CONSUMER_SECRET', $default_values['PESAPAL_CONSUMER_SECRET'], ['class' => 'form-control']); !!}
            </div>
        </div>

        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('PESAPAL_LIVE', 'Is Live?') !!}
                {!! Form::select('PESAPAL_LIVE',['false' => 'False', 'true' => 'True'],  $default_values['PESAPAL_LIVE'], ['class' => 'form-control']); !!}
            </div>
        </div>

        <div class="clearfix"></div>
        
        <h4>Paystack: <small>(For NGN Nigeria, GHS Ghana)</small></h4>
        <div class="col-xs-6">
            <div class="form-group">
                {!! Form::label('PAYSTACK_PUBLIC_KEY', 'Public key:') !!}
                {!! Form::text('PAYSTACK_PUBLIC_KEY', $default_values['PAYSTACK_PUBLIC_KEY'], ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                {!! Form::label('PAYSTACK_SECRET_KEY', 'Secret key:') !!}
                {!! Form::text('PAYSTACK_SECRET_KEY', $default_values['PAYSTACK_SECRET_KEY'], ['class' => 'form-control']); !!}
            </div>
        </div>

        <div class="clearfix"></div>
        
        <h4>Flutterwave:</h4>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('FLUTTERWAVE_PUBLIC_KEY', 'Public key:') !!}
                {!! Form::text('FLUTTERWAVE_PUBLIC_KEY', $default_values['FLUTTERWAVE_PUBLIC_KEY'], ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('FLUTTERWAVE_SECRET_KEY', 'Secret key:') !!}
                {!! Form::text('FLUTTERWAVE_SECRET_KEY', $default_values['FLUTTERWAVE_SECRET_KEY'], ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('FLUTTERWAVE_ENCRYPTION_KEY', 'Encryption key:') !!}
                {!! Form::text('FLUTTERWAVE_ENCRYPTION_KEY', $default_values['FLUTTERWAVE_ENCRYPTION_KEY'], ['class' => 'form-control']); !!}
            </div>
        </div>
        <div class="col-xs-12 mt-0">
            <p class="help-block mt-0">
                <a href="https://support.flutterwave.com/en/articles/3632719-accepted-currencies" target="_blank">
                    @lang('superadmin::lang.flutterwave_help_text')
                </a>
            </p>
        </div>
        <div class="col-xs-12">
            <br/>
            <p class="help-block"><i>@lang('superadmin::lang.payment_gateway_help')</i></p>
        </div>
    </div>
</div>