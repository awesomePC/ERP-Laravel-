<div class="pos-tab-content">
	<div class="row">
		<div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('payroll_ref_no_prefix',  __('essentials::lang.payroll_ref_no_prefix') . ':') !!}
                {!! Form::text('payroll_ref_no_prefix', !empty($settings['payroll_ref_no_prefix']) ? $settings['payroll_ref_no_prefix'] : null, ['class' => 'form-control','placeholder' => __('essentials::lang.payroll_ref_no_prefix')]); !!}
            </div>
        </div>
	</div>
</div>