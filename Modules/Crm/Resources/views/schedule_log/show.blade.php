<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title" id="myModalLabel">
                {{$schedule_log->subject}}
            </h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <strong>@lang('crm::lang.start_datetime'):</strong>
                    {{@format_datetime($schedule_log->start_datetime)}}
                </div>
                <div class="col-md-6">
                    <strong>@lang('crm::lang.end_datetime'):</strong>
                    {{@format_datetime($schedule_log->end_datetime)}}
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-md-12">
                    <div class="box box-solid">
                        <div class="box-body">
                            {!!$schedule_log->description!!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">
                @lang('messages.close')
            </button>
        </div>
    </div>
</div>