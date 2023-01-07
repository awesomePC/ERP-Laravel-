<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label('subject', __('crm::lang.subject') . ':*' )!!}
            {!! Form::text('subject',!empty($proposal_template) ? $proposal_template->subject : '', ['class' => 'form-control', 'required' ]) !!}
       </div>
    </div>
</div>
<div class="row">
	<div class="col-md-12">
        <div class="form-group">
            {!! Form::label('body', __('crm::lang.email_body') . ':*') !!}
            {!! Form::textarea('body', !empty($proposal_template) ? $proposal_template->body : '', ['class' => 'form-control', 'id' => 'proposal_email_body','required']); !!}
        </div>
        @error('body')
        	<label id="proposal_email_body-error" class="error" for="body">
        		{{ $message }}
        	</label>
		@enderror
    </div>
</div>
@if($attachments)
	<div class="row">
		<div class="col-sm-12">
	      <div class="form-group">
	        {!! Form::label('attachments', __('crm::lang.attachments') . ':') !!}
	        {!! Form::file('attachments[]', ['id' => 'attachments', 'accept' => implode(',', array_keys(config('constants.document_upload_mimes_types'))), 'multiple']); !!}
	        <small>
	        	<p class="help-block">
	        		<p class="help-block">
	                    @lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])
	                    @includeIf('components.document_help_text')
	                </p>
	        	</p>
	        </small>
	      </div>
	    </div>
	</div>
@endif