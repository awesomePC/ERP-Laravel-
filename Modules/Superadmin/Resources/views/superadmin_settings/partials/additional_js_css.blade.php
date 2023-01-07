<div class="pos-tab-content">
    <div class="row">
    	<div class="col-xs-12">
            <div class="form-group">
            	{!! Form::label('additional_js', __('superadmin::lang.additional_js') . ':') !!} @show_tooltip(__('superadmin::lang.additional_js_instructions'))
            	{!! Form::textarea('additional_js', !empty($settings['additional_js']) ? $settings['additional_js'] : '', ['class' => 'form-control','placeholder' => __('superadmin::lang.additional_js')]); !!}
            </div>
        </div>
        <div class="col-xs-12">
            <div class="form-group">
            	{!! Form::label('additional_css', __('superadmin::lang.additional_css') . ':') !!} @show_tooltip(__('superadmin::lang.additional_css_instructions'))
            	{!! Form::textarea('additional_css', !empty($settings['additional_css']) ? $settings['additional_css'] : '', ['class' => 'form-control','placeholder' => __('superadmin::lang.additional_css')]); !!}
            </div>
        </div>
    </div>
</div>