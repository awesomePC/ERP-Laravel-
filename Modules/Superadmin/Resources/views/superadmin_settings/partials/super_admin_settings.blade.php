<div class="pos-tab-content active">
    <div class="row">
    	<div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('invoice_business_name', __('business.business_name') . ':') !!}
                <div class="input-group">
                    <span class="input-group-addon">
                        <i class="fa fa-suitcase"></i>
                    </span>
                {!! Form::text('invoice_business_name', $settings["invoice_business_name"], ['class' => 'form-control','placeholder' => __('business.business_name'), 'required']); !!}
            </div>
            </div>
        </div>

        <div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('email', __('business.email'). ':')!!}
                <div class="input-group">
                    <span class="input-group-addon">
                    <i class="fa fa-envelope"></i>
                    </span>
                {!! Form::email('email',$settings["email"], ['class'=>'form-control', 'placeholder'=> __('business.email')])!!}
                </div>
            </div>
        </div>

        <div class="col-xs-4">
            <div class="form-group">
                 {!! Form::label('app_currency_id', __('business.currency') . ':') !!}
                <div class="input-group">
                <span class="input-group-addon">
                    <i class="fas fa-money-bill-alt"></i>
                </span>
                {!! Form::select('app_currency_id', $currencies, $settings["app_currency_id"], ['class' => 'form-control select2','placeholder' => __('business.currency_placeholder'), 'required']); !!}
            </div>
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="col-xs-4">
            <div class="form-group">
                 {!! Form::label('invoice_business_landmark', __('business.landmark') . ':') !!}
                <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-map-marker"></i>
                </span>
                {!! Form::text('invoice_business_landmark', $settings["invoice_business_landmark"], ['class' => 'form-control','placeholder' => __('business.landmark'),'required']); !!}
            </div>
            </div>
        </div> 
        
        <div class="col-xs-4">
            <div class="form-group">
                 {!! Form::label('invoice_business_zip', __('business.zip_code') . ':') !!}
                <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-map-marker"></i>
                </span>
                {!! Form::text('invoice_business_zip',$settings["invoice_business_zip"], ['class' => 'form-control','placeholder' => __('business.zip_code'), 'required']); !!}
            </div>
            </div>
        </div>

        <div class="col-xs-4">
            <div class="form-group">
                 {!! Form::label('invoice_business_state', __('business.state') . ':') !!}
                <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-map-marker"></i>
                </span>
                {!! Form::text('invoice_business_state', $settings["invoice_business_state"], ['class' => 'form-control','placeholder' => __('business.state'), 'required']); !!}
            </div>
            </div>
        </div>

        <div class="col-xs-4">
            <div class="form-group">
                 {!! Form::label('invoice_business_city', __('business.city') . ':') !!}
                <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-map-marker"></i>
                </span>
                {!! Form::text('invoice_business_city',$settings["invoice_business_city"], ['class' => 'form-control','placeholder' => __('business.city'),'required']); !!}
            </div>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                 {!! Form::label('invoice_business_country', __('business.country') . ':') !!}
                <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-globe"></i>
                </span>
                {!! Form::text('invoice_business_country', $settings["invoice_business_country"], ['class' => 'form-control','placeholder' => __('business.country'), 'required']); !!}
            </div>
            </div>
        </div>
        <div class="col-xs-4">
            <div class="form-group">
                 {!! Form::label('package_expiry_alert_days', __('superadmin::lang.package_expiry_alert_days') . ':') !!}
                <div class="input-group">
                <span class="input-group-addon">
                    <i class="fa fa-exclamation-triangle"></i>
                </span>
                {!! Form::number('package_expiry_alert_days', $settings["package_expiry_alert_days"], ['class' => 'form-control','placeholder' => __('superadmin::lang.package_expiry_alert_days'), 'required']); !!}
            </div>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="col-xs-4">
            <div class="form-group">
                <label>
                    {!! Form::checkbox('enable_business_based_username', 1, (int)$settings["enable_business_based_username"] , 
                    [ 'class' => 'input-icheck']); !!} {{ __( 'superadmin::lang.enable_business_based_username' ) }}
                </label>
                <p class="help-block">@lang('superadmin::lang.business_based_username_help')</p>
            </div>
        </div>

        <div class="col-xs-12">
            <p class="help-block"><i>{!! __('superadmin::lang.version_info', ['version' => $superadmin_version]) !!}</i></p>
        </div>
    </div>
</div>