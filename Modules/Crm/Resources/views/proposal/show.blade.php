<div class="modal-dialog modal-lg" role="document">
  	<div class="modal-content">
  		<div class="modal-header no-print">
	      	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
	      		<span aria-hidden="true">&times;</span>
	      	</button>
	      	<h4 class="modal-title no-print">
	      		@lang('crm::lang.proposal_sent_to', ['name' => $proposal->contact])
	      	</h4>
	    </div>
	    <div class="modal-body">
	    	<div class="row">
	    		<div class="col-md-6">
					<p class="pull-left">
						<strong>{{__('crm::lang.sent_by')}}:</strong> {{$proposal->sent_by_full_name}}
					</p>
				</div>
        		<div class="col-md-6">
					<p class="pull-right">
						<strong>{{__('receipt.date')}}:</strong> {{@format_datetime($proposal->created_at)}}
					</p>
				</div>
			</div>
	    	<div class="row mt-10">
        		<div class="col-md-12">
					<p>
						<strong>{{__('crm::lang.subject')}}:</strong> {{$proposal->subject}}
					</p>
				</div>
			</div>
			<div class="row mt-10">
				<div class="col-md-12">
					<p>
						<strong>{{__('crm::lang.email_body')}}:</strong> {!!$proposal->body!!}
					</p>
				</div>
			</div>
			@if($proposal->media->count() > 0)
				<hr>
				<div class="row">
					<div class="col-md-6">
						<h4>
							{{__('crm::lang.attachments')}}
						</h4>
						@includeIf('crm::proposal_template.partials.attachment', ['medias' => $proposal->media])
					</div>
				</div>
			@endif
	    </div>
	    <div class="modal-footer no-print">
	      	<button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
	    </div>
  	</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->