@if($schedule_logs->count() > 0)
    @foreach($schedule_logs as $schedule_log)
        <!-- timeline time label -->
        <li class="time-label">
            <span class="bg-red">
                {{@format_datetime($schedule_log->created_at)}}
            </span>
        </li>
        <!-- /.timeline-label -->

        <!-- timeline item -->
        <li>
            <!-- timeline icon -->
            <i class="
                @if($schedule_log->log_type == 'email')
                    fa fa-envelope
                @elseif($schedule_log->log_type == 'call')
                    fas fa fa-phone-alt
                @elseif($schedule_log->log_type == 'sms')
                    fas fa fa-sms
                @elseif($schedule_log->log_type == 'meeting')
                    fas fa fa-handshake
                @endif
                @if($schedule_log->created_at == $schedule_log->updated_at)
                    bg-green
                @else
                    bg-blue
                @endif
                " data-toggle="tooltip" title="@lang('crm::lang.'.$schedule_log->log_type)">
            </i>
            <div class="timeline-item">
                <span class="time pa-0">
                    <span>
                        <i class="fas fa-pen"></i>
                        {{$schedule_log->createdBy->user_full_name}}
                    </span><br>
                    <i class="fas fa-clock"></i>
                    {{@format_datetime($schedule_log->start_datetime)}} ~ {{@format_datetime($schedule_log->end_datetime)}}
                </span>

                <h3 class="timeline-header">
                    <a class="cursor-pointer view_a_schedule_log" data-href="{{action('\Modules\Crm\Http\Controllers\ScheduleLogController@show', ['id' => $schedule_log->id, 'schedule_id' => $schedule_log->schedule_id])}}">
                        {{$schedule_log->subject}}
                    </a>
                </h3>

                <div class="timeline-body">
                    {!!$schedule_log->description!!}
                </div>

                <div class="timeline-footer">
                    
                    <i class="fa fa-eye cursor-pointer m-5 text-info view_a_schedule_log" data-href="{{action('\Modules\Crm\Http\Controllers\ScheduleLogController@show', ['id' => $schedule_log->id, 'schedule_id' => $schedule_log->schedule_id])}}"></i>
                
                
                    <i class="fa fa-edit cursor-pointer m-5 text-primary edit_schedule_log" data-href="{{action('\Modules\Crm\Http\Controllers\ScheduleLogController@edit', ['id' => $schedule_log->id, 'schedule_id' => $schedule_log->schedule_id])}}"></i>
            
              
                    <i class="fas fa-trash cursor-pointer m-5 text-danger delete_schedule_log" data-href="{{action('\Modules\Crm\Http\Controllers\ScheduleLogController@destroy', ['id' => $schedule_log->id, 'schedule_id' => $schedule_log->schedule_id])}}"></i>
                    
                </div>
            </div>
        </li>
        <!-- END timeline item -->
    @endforeach
    @if($schedule_logs->nextPageUrl())
        <li class="timeline-lode-more-btn">
            <a data-href="{{$schedule_logs->nextPageUrl()}}" class="btn btn-block btn-sm btn-info load_more_log">
                @lang('project::lang.load_more')
            </a>
        </li>
    @endif
@else
    <li>
        <div class="timeline-item">
            <div class="timeline-body">
                <span class="text-info">@lang('crm::lang.no_log_found')</span>
            </div>
        </div>
    </li>
@endif