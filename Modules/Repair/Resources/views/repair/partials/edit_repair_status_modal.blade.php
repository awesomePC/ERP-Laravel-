<div class="modal-dialog" role="document">
	<div class="modal-content">

		{!! Form::open(['url' => action('\Modules\Repair\Http\Controllers\RepairController@updateRepairStatus'), 'method' => 'post', 'id' => 'update_repair_status_form']) !!}
			{!! Form::hidden('repair_id', $transaction->id, ['id' => 'repair_id']); !!}
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">@lang( 'repair::lang.edit_status' )</h4>
			</div>

			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<strong>@lang('sale.invoice_no'): </strong>
						<span id="repair_no">{{$transaction->invoice_no}}</span>
					</div>
				</div>
				<div class="row mt-15">
					<div class="col-md-12">
						<div class="form-group">
							{!! Form::label('repair_status_id_modal',  __('sale.status') . ':*') !!}
		            		{!! Form::select('repair_status_id_modal', $repair_status_dropdown['statuses'], $transaction->repair_status_id, ['class' => 'form-control select2', 'required', 'style' => 'width:100%', 'placeholder' => __('messages.please_select'), 'required'], $repair_status_dropdown['template']); !!}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
		    				{!! Form::label('update_note', __( 'repair::lang.update_note' ) . ':') !!}
		      				{!! Form::textarea('update_note', null, ['class' => 'form-control', 'placeholder' => __( 'repair::lang.update_note' ), 'rows' => 3, 'id' => 'update_note' ]); !!}
		  				</div>
		  			</div>
		  		</div>
  				<div class="row">
	                <div class="col-md-4">
	                    <div class="form-group">
	                        <div class="checkbox">
	                            <label>
	                                <input type="checkbox" name="send_sms" value="1" id="send_sms"> @lang('repair::lang.send_sms')
	                            </label>
	                        </div>
	                    </div>
	                </div>
	                <div class="col-md-12 sms_body" style="display: none !important;">
	                	<div class="form-group">
	        				{!! Form::label('sms_body', __( 'lang_v1.sms_body' ) . ':') !!}
	          				{!! Form::textarea('sms_body', null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.sms_body' ), 'rows' => 4, 'id' => 'sms_body']); !!}
	          				<p class="help-block">
		                        <label>{{$status_template_tags['help_text']}}:</label><br>
		                        {{implode(', ', $status_template_tags['tags'])}}
		                    </p>
	      				</div>
	                </div>
	            </div>
			</div>

			<div class="modal-footer">
				<button type="submit" class="btn btn-primary ladda-button">@lang( 'messages.update' )</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
			</div>

		{!! Form::close() !!}

	</div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->