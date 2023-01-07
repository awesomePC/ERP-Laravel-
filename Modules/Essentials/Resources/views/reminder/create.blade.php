<div class="modal fade reminder" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
  {!! Form::open(['url' => action('\Modules\Essentials\Http\Controllers\ReminderController@store'), 'id' => 'reminder_form']) !!}
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="exampleModalCenterTitle">
            @lang('essentials::lang.add_reminder')
          </h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          @php
            $repeat = [
                      'one_time' => __('essentials::lang.one_time'),
                      'every_day' => __('essentials::lang.every_day'),
                      'every_week' => __('essentials::lang.every_week'),
                      'every_month' => __('essentials::lang.every_month'),
                        ];
            @endphp
          <div class="row">
            <div class="col-md-12">
              {!! Form::label('name', __('essentials::lang.event_name') . ":*") !!}

                      {!! Form::text('name', null, ['class' => 'form-control', 'required']) !!}
            </div>
            <div class="clearfix"></div>
            <div class="col-md-6">
              {!! Form::label('repeat', __('essentials::lang.repeat') . ':*') !!}
              {!! Form::select('repeat', $repeat, null, ['class' => 'form-control','required']) !!}
            </div>
            <div class="col-md-6">
              <div class="form-group">
                {!! Form::label('date', __('essentials::lang.date') . ':*') !!}
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </span>
                  {!! Form::text('date', @format_date('today'), ['class' => 'form-control datepicker', 'required', 'readonly']); !!}
                </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-6">
              <div class="form-group">
                {!! Form::label('time', __('restaurant.start_time') . ':*') !!}
                      <div class='input-group'>
                        <span class="input-group-addon">
                              <span class="glyphicon glyphicon-time"></span>
                          </span>
                {!! Form::text('time', @format_time('now'), ['class' => 'form-control', 'required', 'id' => 'time', 'readonly']); !!}
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                {!! Form::label('time', __('restaurant.end_time') . ':') !!}
                      <div class='input-group'>
                        <span class="input-group-addon">
                              <span class="glyphicon glyphicon-time"></span>
                          </span>
                {!! Form::text('end_time', @format_time('now'), ['class' => 'form-control', 'id' => 'end_time', 'readonly']); !!}
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            @lang('essentials::lang.cancel')
          </button>
          <button type="submit" class="btn btn-primary save_reminder">
            @lang('essentials::lang.submit')
          </button>
        </div>
      </div>
      {!! Form::close() !!}
  </div>
</div>