<div class="pos-tab-content">
    <div class="row">
        <div class="col-xs-6">
            <div class="checkbox">
                <label>
                    {!! Form::checkbox('is_location_required', 1, !empty($settings['is_location_required']) ? 1 : 0, ['class' => 'input-icheck'] ); !!} @lang('essentials::lang.is_location_required')
                </label>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-12">
            <strong>@lang('essentials::lang.grace_time'):</strong>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                {!! Form::label('grace_before_checkin',  __('essentials::lang.grace_before_checkin') . ':') !!}
                {!! Form::number('grace_before_checkin', !empty($settings['grace_before_checkin']) ? $settings['grace_before_checkin'] : null, ['class' => 'form-control','placeholder' => __('essentials::lang.grace_before_checkin'), 'step' => 1]); !!}
                <p class="help-block">@lang('essentials::lang.grace_before_checkin_help')</p>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                {!! Form::label('grace_after_checkin',  __('essentials::lang.grace_after_checkin') . ':') !!}
                {!! Form::number('grace_after_checkin', !empty($settings['grace_after_checkin']) ? $settings['grace_after_checkin'] : null, ['class' => 'form-control','placeholder' => __('essentials::lang.grace_after_checkin'), 'step' => 1]); !!}
                <p class="help-block">@lang('essentials::lang.grace_after_checkin_help')</p>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                {!! Form::label('grace_before_checkout',  __('essentials::lang.grace_before_checkout') . ':') !!}
                {!! Form::number('grace_before_checkout', !empty($settings['grace_before_checkout']) ? $settings['grace_before_checkout'] : null, ['class' => 'form-control','placeholder' => __('essentials::lang.grace_before_checkout'), 'step' => 1]); !!}
                <p class="help-block">@lang('essentials::lang.grace_before_checkout_help')</p>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                {!! Form::label('grace_after_checkout',  __('essentials::lang.grace_after_checkout') . ':') !!}
                {!! Form::number('grace_after_checkout', !empty($settings['grace_after_checkout']) ? $settings['grace_after_checkout'] : null, ['class' => 'form-control','placeholder' => __('essentials::lang.grace_after_checkout'), 'step' => 1]); !!}
                <p class="help-block">@lang('essentials::lang.grace_before_checkin_help')</p>
            </div>
        </div>
    </div>
    <p>
        <i class="fas fa-info-circle"></i>
      <span class="text-danger">@lang('essentials::lang.allow_users_for_attendance_moved_to_role')</span>
    </p>
</div>