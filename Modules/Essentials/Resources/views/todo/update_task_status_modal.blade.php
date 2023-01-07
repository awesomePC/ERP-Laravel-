<div class="modal fade" id="update_task_status_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog" role="document">
  		<div class="modal-content">
  			<div class="modal-header">
		      	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		      	<h4 class="modal-title">@lang( 'essentials::lang.change_status' )</h4>
		    </div>
		    <div class="modal-body">
	  			<div class="form-group">
					{!! Form::label('updated_status', __('sale.status') . ':') !!}
					{!! Form::select('status', $task_statuses, null, ['class' => 'form-control', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;', 'id' => 'updated_status']); !!}
					{!! Form::hidden('task_id', null, ['id' => 'task_id']); !!}
				</div>
  			</div>
  			<div class="modal-footer">
		      	<button type="button" class="btn btn-primary" id="update_status_btn">@lang( 'messages.update' )</button>
		      	<button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
		    </div>
  		</div>
  	</div>
</div>