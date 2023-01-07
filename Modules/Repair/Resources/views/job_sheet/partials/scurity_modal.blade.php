<div class="modal fade" id="security_pattern" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">
            @lang('repair::lang.security_pattern')
        </h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
            {!! Form::label('security_pattern', __('repair::lang.pattern') . ':') !!}
            <div id="pattern_container"></div>
            {!! Form::hidden('security_pattern', !empty($job_sheet->security_pattern) ? $job_sheet->security_pattern : null ); !!}
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">
            @lang('messages.close')
        </button>
        <button type="button" class="btn btn-primary" data-dismiss="modal">
            @lang('messages.save')
        </button>
      </div>
    </div>
  </div>
</div>