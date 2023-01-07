<div class="pos-tab-content">
    <div class="row">
    	<div class="col-xs-4">
            <div class="form-group">
                {!! Form::label('essentials_todos_prefix',  __('essentials::lang.essentials_todos_prefix') . ':') !!}
                {!! Form::text('essentials_todos_prefix', !empty($settings['essentials_todos_prefix']) ? $settings['essentials_todos_prefix'] : null, ['class' => 'form-control','placeholder' => __('essentials::lang.essentials_todos_prefix')]); !!}
            </div>
        </div>
    </div>
</div>