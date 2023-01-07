<div class="modal-dialog" role="document">
  	<div class="modal-content">
  		{!! Form::open(['url' => action('\Modules\Essentials\Http\Controllers\SalesTargetController@saveSalesTarget'), 'method' => 'post' ]) !!}
  		<input type="hidden" name="user_id" value="{{$user->id}}">
  		<div class="modal-header">
	      	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      	<h4 class="modal-title">
	      		@lang( 'essentials::lang.set_sales_target_for', ['user' => $user->user_full_name] )
	      	</h4>
	    </div>
	    <div class="modal-body">
	    	<table class="table" id="target_table">
	    		<thead>
	    			<tr>
	    				<th>
	    					@lang( 'essentials::lang.total_sales_amount_from')
	    				</th>
	    				<th>
	    					@lang( 'essentials::lang.total_sales_amount_to')
	    				</th>
	    				<th>
	    					@lang( 'essentials::lang.commission_percent')
	    				</th>
	    				<th>
	    					<button type="button" class="btn btn-primary btn-sm" id="add_target"><i class="fas fa-plus"></i></button>
	    				</th>
	    			</tr>
	    			</thead>
	    			<tbody>
	    				@foreach($sales_targets as $sales_target)
	    					<tr>
			    				<td>
			    					{!! Form::text('edit_target[' . $sales_target->id . '][target_start]', @num_format($sales_target->target_start), ['class' => 'form-control input-sm input_number', 'required']); !!}
			    				</td>
			    				<td>
			    					{!! Form::text('edit_target[' . $sales_target->id . '][target_end]', @num_format($sales_target->target_end), ['class' => 'form-control input-sm input_number', 'required']); !!}
			    				</td>
			    				<td>
			    					{!! Form::text('edit_target[' . $sales_target->id . '][commission_percent]', @num_format($sales_target->commission_percent), ['class' => 'form-control input-sm input_number', 'required']); !!}
			    				</td>
			    				<td>
									<button type="button" class="btn btn-danger btn-xs remove_target"><i class="fas fa-times"></i></button>
								</td>
			    			</tr>
	    				@endforeach
		    			<tr>
		    				<td>
		    					{!! Form::text('sales_amount_start[]', 0, ['class' => 'form-control input-sm input_number', 'required']); !!}
		    				</td>
		    				<td>
		    					{!! Form::text('sales_amount_end[]', 0, ['class' => 'form-control input-sm input_number', 'required']); !!}
		    				</td>
		    				<td>
		    					{!! Form::text('commission[]', 0, ['class' => 'form-control input-sm input_number', 'required']); !!}
		    				</td>
		    			</tr>
	    			</tbody>
	    	</table>
	    </div>
	    <div class="modal-footer">
	      	<button type="submit" class="btn btn-primary">@lang( 'messages.submit' )</button>
	      	<button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
	    </div>
	    {!! Form::close() !!}
	    <table class="hidden" id="sales_target_row_hidden">
	    	<tr>
				<td>
					{!! Form::text('sales_amount_start[]', 0, ['class' => 'form-control input-sm input_number', 'required']); !!}
				</td>
				<td>
					{!! Form::text('sales_amount_end[]', 0, ['class' => 'form-control input-sm input_number', 'required']); !!}
				</td>
				<td>
					{!! Form::number('commission[]', 0, ['class' => 'form-control input-sm', 'min' => 0, 'max' => 100, 'required']); !!}
				</td>
				<td>
					<button type="button" class="btn btn-danger btn-xs remove_target"><i class="fas fa-times"></i></button>
				</td>
			</tr>
	    </table>
  	</div>
</div>