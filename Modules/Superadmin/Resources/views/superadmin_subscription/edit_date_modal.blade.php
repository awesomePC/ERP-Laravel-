<!-- Modal -->
<div class="modal-dialog" role="document">
    <div class="modal-content edit-subscription-modal">
     {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\SuperadminSubscriptionsController@updateSubscription'), 'method' => 'POST', 'id' => 'edit_subscription_form']) !!}
      {!! Form::hidden('subscription_id', $subscription->id); !!}
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">@lang( "superadmin::lang.edit_subscription")</h4>
      </div>
      <div class="modal-body">
             <div class="form-group">
                {!! Form::label('start_date', __( "superadmin::lang.start_date")) !!}

                {!! Form::text('start_date', !empty($subscription->start_date) ? @format_date($subscription->start_date) : null, ['class' => 'form-control datepicker', 'readonly']); !!}
              </div>

              <div class="form-group">
                {!! Form::label('end_date', __("superadmin::lang.end_date"))!!}

                {!! Form::text('end_date', !empty($subscription->end_date) ? @format_date($subscription->end_date) : null, ['class' => 'form-control datepicker', 'readonly']);!!}
              </div>

              <div class="form-group">
                {!! Form::label('trial_end_date', __("superadmin::lang.trial_end_date"))!!}

                {!! Form::text('trial_end_date', !empty($subscription->trial_end_date) ? @format_date($subscription->trial_end_date) : null, ['class' => 'form-control datepicker', 'readonly']);!!}
              </div>
      </div>

      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">@lang( "superadmin::lang.update")</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( "superadmin::lang.close")</button>
      </div>
      {!! Form::close() !!}
    </div>
</div>
