<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action('\Modules\Crm\Http\Controllers\ScheduleLogController@update', ['id' => $schedule_log->id]), 'method' => 'put', 'id' => 'schedule_log_form' ]) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    @lang('crm::lang.edit_schedule_log')
                </h4>
            </div>
            <div class="modal-body">
                <!-- schedule id -->
                <input type="hidden" name="schedule_id" value="{{$schedule->id}}">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('subject', __('crm::lang.subject') . ':*') !!}
                            {!! Form::text('subject', $schedule_log->subject, ['class' => 'form-control', 'required']); !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('log_type', __('crm::lang.log_type') .':*') !!}
                            {!! Form::select('log_type', ['call' => __('crm::lang.call'), 'sms' => __('crm::lang.sms'), 'meeting' => __('crm::lang.meeting'), 'email' => __('business.email')], $schedule_log->log_type, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>
                    <div class="col-md-4">
                       <div class="form-group">
                            {!! Form::label('start_datetime', __('crm::lang.start_datetime') . ':*' )!!}
                            {!! Form::text('start_datetime', !empty($schedule_log->start_datetime) ? @format_datetime($schedule_log->start_datetime) : '', ['class' => 'form-control datetimepicker', 'required', 'readonly']) !!}
                       </div>
                    </div>
                    <div class="col-md-4">
                       <div class="form-group">
                            {!! Form::label('end_datetime', __('crm::lang.end_datetime') . ':*' )!!}
                            {!! Form::text('end_datetime', !empty($schedule_log->end_datetime) ? @format_datetime($schedule_log->end_datetime) : '', ['class' => 'form-control datetimepicker', 'required', 'readonly']) !!}
                       </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('description', __('lang_v1.description') . ':') !!}
                            {!! Form::textarea('description', $schedule_log->description, ['class' => 'form-control ', 'id' => 'description']); !!}
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4">
                       <div class="form-group">
                            {!! Form::label('status', __('crm::lang.schedule_status') .':') !!}
                            {!! Form::select('status', $statuses, $schedule->status, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;']); !!}
                       </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                @lang('messages.close')
                </button>
                <button type="submit" class="btn btn-primary">
                    @lang('messages.update')
                </button>
            </div>
        {!! Form::close() !!}
    </div>
</div>